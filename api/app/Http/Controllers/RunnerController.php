<?php

namespace App\Http\Controllers;

use App\Runner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RunnerController extends Controller
{
    /** 
     * @var Runner $runner
    */
    private $runner;

    /**
     * Create a new controller instance.
     *
     * @param Runner $runner
     * @return void
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }


    /**
     * List all runners.
     *
     * @param Request $request
     * @return mixed
     */
    public function findAll(Request $request)
    {
        $runners = $this->runner->all();

        return $runners;
    }

    /**
     * Insert a new runner.
     *
     * @param Request $request
     * @return mixed
     */
    public function insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'cpf' => 'required',
            'birthdate' => 'required',
        ], [
            "Campo name obrigatório",
            "Campo CPF obrigatório",
            "Campo birthdate obrigatório",
        ]);

        $params = $request->all();

        if (Carbon::parse($params['birthdate'])->age < 18) {
            return response()->json([
                "message" => "Não é permitido cadastrar um corredor menor de 18 anos."
            ], Response::HTTP_CONFLICT);
        }

        $runner = $this->runner
            ->where('cpf', $params['cpf'])
            ->first();

        if(!empty($runner))
            return response()->json([
                "message" => "CPF já cadastrado"
            ], Response::HTTP_CONFLICT);

        $this->runner->fill($params)->save();

        return $this->runner;
    }
}
