<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;
use App\Http\Requests;
use App\Http\Requests\RunRequest;
use App\Application;
use App\Chemistry;
use App\Run_status;
use App\Instrument;
use App\Iem_file_version;
use App\Work_flow;
use App\Assay;
use App\Adaptor;
use App\SampleRun;
use App\ProjectGroup;
use App\Run;
use Illuminate\Http\Response;
use App\Batch;

class RunsController extends Controller
{

    // Restrict access to authenticated users
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('super', ['except' => ['index','show']]);
    }

    /*
    *  For custom error messages see "resources/lang/en/validation.php"
    *
    *  For validation see "Http/Requests/RunRequest.php"
    */

    /**
     * Display a listing of Runs.
     *
     * @return Response
     */
    public function index()
    {
        // Show 10 latest samples
        $runs = Run::orderBy('run_date', 'DESC')->take(100)->get();

        return view('runs.index', ['runs' => $runs]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // Show 10 latest samples
        $runs = Run::orderBy('created_at', 'DESC')->take(10)->get();

        return view('runs.index', ['runs' => $runs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $run = Run::where('id', $id)->first();

        // query selects batch name, date, charge code, user and project for run having status updated.
        // smaller return than eloquent query were only small sub set of data required.
        $batches = DB::table('batches')
            ->join('samples', function ($join) {
                $join->on('batches.id','=', 'samples.batch_id');
            })
            ->join('sample_runs', function ($join) {
                $join->on('sample_runs.sample_id','=', 'samples.id');
            })
            ->join('users',function ($join) {
                $join->on('batches.user_id','=', 'users.id');
            })
            ->join('project_group',function ($join) {
                $join->on('batches.project_group_id','=', 'project_group.id');
            })
            ->where('sample_runs.run_id', '=', $run->id)
            ->distinct('batches.id')
           ->select('batches.id','batch_name','batches.created_at','batches.charge_code','users.name','project_group.name as project')
            ->get();

        $status_options = Run_status::lists('status', 'id');

        return view('runs.edit', [
            'run' => $run,
            'status_options' => $status_options,
            'batches' => $batches
        ]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function setStatus(Request $request)
    {
        $input = $request->all();
        $run = Run::where('id', $input['run_id'])->first();



        // existing run status is either run built or run succeeded and new value is run failed
        // increment all samples in run by one
        if(($run->run_status_id ==1 ||$run->run_status_id ==1)&&  $input['run_status']==3)
        {
            $samples = DB::table('samples')->select('samples.id','runs_remaining')
                ->join('sample_runs', function ($join) {
                    $join->on('samples.id','=', 'sample_runs.sample_id');
                })
                ->join('runs', function ($join) {
                    $join->on('runs.id','=', 'sample_runs.run_id');
                })
                ->where('runs.id', '=', $input['run_id'])
                ->get();


            foreach ($samples as $sample)
            {
                DB::table('samples')
                    ->where('id', $sample->id)
                    ->update(['runs_remaining' => ($sample->runs_remaining +1)]);
            }
        }

        $run->run_status_id = $input['run_status'];


        $run->update();

        return redirect('runs');
    }
}
