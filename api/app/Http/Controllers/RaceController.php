<?php

namespace App\Http\Controllers;

use App\Race;
use App\Runner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RaceController extends Controller
{
    /** 
     * @var Race $race
    */
    private $race;

    /**
     * Create a new controller instance.
     *
     * @param Race $race
     * 
     * @return void
     */
    public function __construct(Race $race)
    {
        $this->race = $race;
    }


    /**
     * List all results sorted by duration and organized by age category for each race.
     *
     * @param Request $request
     * 
     * @return mixed
     */
    public function listResultByAge(Request $request)
    {
        $races = $this->race->all();

        $results = [];

        foreach ($races as $race) {
            $runners = $race->runners()
                            ->whereNotNull('start_time')
                            ->whereNotNull('finish_time')
                            ->orderBy(DB::raw('TIMEDIFF(race_runner.finish_time, race_runner.start_time)'))
                            ->get();

            $raceResult = array(
                'id_race' => $race->id,
                'type' => $race->type,
                'results' => array(
                    'cat_18_25' => array(),
                    'cat_26_35' => array(),
                    'cat_36_45' => array(),
                    'cat_46_55' => array(),
                    'cat_55' => array()
                )
            );

            foreach ($runners as $runner) {
                $runnerAge = Carbon::parse($runner->birthdate)->age;

                $runnerClassification = [
                    'id_runner' => $runner->pivot->id_runner,
                    'runner_age' => $runnerAge,
                    'runner_name' => $runner->name,
                ];

                $ageKey = $this->getAgeGroup($runnerAge);

                if (empty($ageKey)) {
                    return response()->json([
                        'message' => 'Aconteceu um erro ao calcular a idade do corredor.'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $arrayByAgeLength = count($raceResult['results'][$ageKey]);

                $runnerClassification['position'] = $arrayByAgeLength + 1;

                $raceResult['results'][$ageKey][] = $runnerClassification;
            }

            $results[] = $raceResult;
        }

        return $results;
    }

    /**
     * List all results sorted by duration for each race.
     *
     * @param Request $request
     * 
     * @return mixed
     */
    public function listGeneralResult(Request $request)
    {
        $races = $this->race->all();

        $results = [];

        foreach ($races as $race) {
            $runners = $race->runners()
                            ->whereNotNull('start_time')
                            ->whereNotNull('finish_time')
                            ->orderBy(DB::raw('TIMEDIFF(race_runner.finish_time, race_runner.start_time)'))
                            ->get();

            $raceResult = array(
                'id_race' => $race->id,
                'type' => $race->type,
                'results' => array()
            );

            foreach ($runners as $runner) {
                $runnerAge = Carbon::parse($runner->birthdate)->age;

                $runnerClassification = [
                    'id_runner' => $runner->pivot->id_runner,
                    'runner_age' => $runnerAge,
                    'runner_name' => $runner->name,
                ];

                $arrayByAgeLength = count($raceResult['results']);

                $runnerClassification['position'] = $arrayByAgeLength + 1;

                $raceResult['results'][] = $runnerClassification;
            }

            $results[] = $raceResult;
        }

        return $results;
    }

    /**
     * Validate age and return group name.
     *
     * @param int $runnerAge
     * 
     * @return string|null
     */
    public function getAgeGroup($runnerAge)
    {
        if ($runnerAge >= 18 && $runnerAge <= 25) {
            return 'cat_18_25';
        } else if($runnerAge > 25 && $runnerAge <= 35) {
            return 'cat_26_35';
        } else if($runnerAge > 35 && $runnerAge <= 45) {
            return 'cat_36_45';
        } else if($runnerAge > 45 && $runnerAge <= 55) {
            return 'cat_46_55';
        } else if($runnerAge > 55) {
            return 'cat_55';
        }

        return null;
    }

    /**
     * Insert a new race.
     *
     * @param Request $request
     * 
     * @return mixed
     */
    public function insert(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'event_date' => 'required',
        ], [
            "Campo type obrigatório",
            "Campo event_date obrigatório",
        ]);

        $params = $request->all();

        $this->race->fill($params)->save();

        return $this->race;
    }

    /**
     * Add a new runner in a race.
     *
     * @param Request $request
     * 
     * @return mixed
     */
    public function addRunner(Request $request)
    {
        $this->validate($request, [
            'id_race' => 'required',
            'id_runner' => 'required',
        ], [
            "Campo id_race obrigatório",
            "Campo id_runner obrigatório",
        ]);

        $params = $request->all();

        $race = $this->race->find($params['id_race']);

        if (empty($race)) {
            return response()->json([
                'message' => 'Corrida não encontrada.'
            ], Response::HTTP_NOT_FOUND);
        }

        $raceDate = $race->event_date;

        $racesSameData = $this->race->whereDate('event_date', '=', $raceDate)
                                    ->whereHas('runners', function($query) use($params) {
                                        $query->where('id_runner', '=', $params['id_runner']);
                                    })->first();

        if (!empty($racesSameData)) {
            return response()->json([
                'message' => 'Esse corredor já está cadastrado em uma corrida na mesma data.'
            ], Response::HTTP_CONFLICT);
        }

        $race->runners()->attach($params['id_runner']);

        return $race->runners()->find($params['id_runner'])->pivot;
    }

    /**
     * Insert results of a runner in a race.
     *
     * @param Request $request
     * 
     * @return mixed
     */
    public function insertResults(Request $request)
    {
        $this->validate($request, [
            'id_race' => 'required',
            'id_runner' => 'required',
            'start_time' => 'required',
            'finish_time' => 'required',
        ], [
            "Campo id_race obrigatório",
            "Campo id_runner obrigatório",
            "Campo start_time obrigatório",
            "Campo finish_time obrigatório",
        ]);

        $params = $request->all();

        $race = $this->race->find($params['id_race']);

        $race->runners()->updateExistingPivot($params['id_runner'], [
            'start_time' => $params['start_time'], 
            'finish_time' => $params['finish_time']
        ]);

        return $race->runners()->find($params['id_runner'])->pivot;
    }
}
