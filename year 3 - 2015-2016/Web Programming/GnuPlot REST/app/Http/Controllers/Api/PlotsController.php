<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\Template;
use App\Services\Plotter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlotsController extends Controller
{

    /**
     * List all of the plots
     */
    public function all()
    {
        return Plot::with('template')->orderBy('id', 'desc')->get();
    }

    /**
     * Create a new plot
     *
     * @param Request $request
     * @param Plotter $plotter
     * @return Plot
     */
    public function store(Request $request, Plotter $plotter)
    {
        $this->validate($request, [
            'name' => 'required',
            'data'  => 'required',
            'template' => 'required|exists:templates,id',
            'password' => 'required',
        ]);

        // We can create the plot
        $template = Template::find($request->get('template'));

        $plot = new Plot();
        $plot->name = $request->get('name');
        $plot->template = $template->id;
        $plot->password = $request->get('password');
        $plot->save();

        // Generate plotter image
        $plotter->generate($plot, $template, $request->file('data')->getPathname());

        return $plot;
    }

    /**
     * Update the plot
     *
     * @param $id
     * @param Request $request
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
        ]);

        $plot = Plot::where('id', $id)->first();

        if ($plot == null) {
            return response(['message' => 'Plot not found!'], 404);
        }

        if ($plot->password != $request->get('password')) {
            return response(['message' => 'Invalid password'], 422);
        }

        $plot->name = $request->get('name');
        $plot->save();

        return $plot;
    }

    /**
     * Find the plot by id
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $plot = Plot::with('template')->where('id', $id)->first();

        if ($plot == null) {
            return response(['message' => 'Plot not found!'], 404);
        }

        return $plot;
    }

    /**
     * Delete plot by id and password
     *
     * @param $id
     * @param Request $request
     * @return array|JsonResponse
     */
    public function delete($id, Request $request)
    {
        $plot = Plot::where('id', $id)->first();

        if ($plot == null) {
            return response(['message' => 'Plot not found!'], 404);
        }

        if ($plot->password != $request->get('password')) {
            return response(['message' => 'Invalid password!'], 422);
        }

        $plot->delete();

        return ['message' => 'Plot deleted!'];
    }

    /**
     * Search for plots by their name
     *
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        return Plot::where('name', 'LIKE', '%'.$request->get('query').'%')->get();
    }

    /**
     * Download the script of the plot
     *
     * @param $id
     * @return mixed
     */
    public function script($id)
    {
        $plot = Plot::with('template')->where('id', $id)->first();
        $template = Template::where('id', $plot->template)->first();

        if ($plot == null || $template == null) {
            return response(['message' => 'Plot not found!'], 404);
        }


        $response = response($template->script);
        $response->header('Content-Disposition', 'attachment; filename="script.gp"\')');
        $response->header('Content-Type', 'text/plain');
        $response->header('Content-Length', strlen($template->script));
        $response->header('Connection', 'close');

        return $response;
    }

}
