<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\Plotter;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{

    /**
     * List all of the templates
     *
     * @return Template[]
     */
    public function all()
    {
        return Template::all();
    }

    /**
     * Get the template by id
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $template = Template::where('id', $id)->first();

        if ($template == null) {
            return response(['message' => 'Template not found!'], 404);
        }

        return $template;
    }

    /**
     * Download the template script
     *
     * @param $id
     */
    public function script($id)
    {
        $template = Template::where('id', $id)->first();

        if ($template == null) {
            return response(['message' => 'Template not found!'], 404);
        }

        $response = response($template->script);
        $response->header('Content-Disposition', 'attachment; filename="script.gp"\')');
        $response->header('Content-Type', 'text/plain');
        $response->header('Content-Length', strlen($template->script));
        $response->header('Connection', 'close');

        return $response;
    }

    /**
     * Preview the plot
     *
     * @param $id
     * @param Request $request
     * @param Plotter $plotter
     * @return mixed
     */
    public function preview($id, Request $request, Plotter $plotter)
    {
        $this->validate($request, [
           'data' => 'required',
        ]);

        $template = Template::where('id', $id)->first();

        if ($template == null) {
            return response(['message' => 'Template not found!'], 404);
        }

        $imageData = $plotter->preview($template, $request->file('data')->getPathname());

        return response([
            'image' => $imageData
        ]);
    }
}
