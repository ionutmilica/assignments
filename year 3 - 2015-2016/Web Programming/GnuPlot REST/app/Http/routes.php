<?php

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="gnuplot.dev",
 *     basePath="/api",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="GnuPlot API",
 *         description="Api pentru interfata web GnuPlot",
 *             @SWG\License(name="MIT")
 *     ),
 *     @SWG\Definition(
 *         definition="error",
 *         required={"message"},
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     ),
 *     @SWG\Definition(
 *         definition="url",
 *         required={"url"},
 *         @SWG\Property(
 *             property="url",
 *             type="string"
 *         )
 *     ),
 *     @SWG\Definition(
 *         definition="image",
 *         required={"image"},
 *         @SWG\Property(
 *             property="image",
 *             type="string"
 *         )
 *     ),
 * )
 */
$app->group(['prefix' => 'api', 'namespace' => 'Api'], function ($app) {

    $app->get('/', [
        'as' => 'api.swagger',
        'uses' => 'HomeController@swagger',
    ]);

    /**
     * @SWG\Get(
     *     path="/search",
     *     summary="Find plot by name",
     *     tags={"plots"},
     *     description="Find plot by matching the name against the plots stored in the database.",
     *     operationId="searchPlot",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="query",
     *         in="query",
     *         description="The name of the plot",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Plot object",
     *         @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/plot"))
     *     )
     * )
     */
    $app->get('search', 'PlotsController@search');


    $app->group(['prefix' => 'plots'], function ($app) {
        /**
         * @SWG\Get(
         *     path="/plots",
         *     summary="Get the list of the plots",
         *     tags={"plots"},
         *     description="List of all create plots.",
         *     operationId="allPlots",
         *     produces={"application/json"},
         *     @SWG\Response(
         *         response=200,
         *         description="Plot object",
         *         @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/plot"))
         *     )
         * )
         */
         $app->get('/', 'PlotsController@all');


        /**
         * @SWG\Post(
         *     path="/plots",
         *     summary="Create a new plot",
         *     tags={"plots"},
         *     operationId="addPlot",
         *     description="Creates a new plot into the database",
         *     consumes={"multipart/form-data"},
         *     produces={"application/json"},
         *   @SWG\Parameter(
         *     name="name",
         *     in="formData",
         *     description="Plot name",
         *     required=true,
         *     type="string"
         *   ),
         *   @SWG\Parameter(
         *     name="template",
         *     in="formData",
         *     description="The plot template",
         *     required=true,
         *     type="integer"
         *   ),
         *   @SWG\Parameter(
         *     name="data",
         *     in="formData",
         *     description="Data file",
         *     required=true,
         *     type="file"
         *   ),
         *   @SWG\Parameter(
         *     name="password",
         *     in="formData",
         *     description="The plot password",
         *     required=true,
         *     type="string"
         *   ),
         *     @SWG\Response(
         *         response="422",
         *         description="unexpected error",
         *         @SWG\Schema(ref="#/definitions/error")
         *     )
         * )
         */
        $app->post('/', 'PlotsController@store');

        /**
         * @SWG\Delete(path="/plots/{id}",
         *   tags={"plots"},
         *   summary="Delete plot",
         *   description="Deletes a plot using an id and a password",
         *   operationId="deletePlot",
         *   consumes={"application/x-www-form-urlencoded"},
         *   produces={"application/json"},
         *   @SWG\Parameter(
         *     name="id",
         *     format="int64",
         *     in="path",
         *     description="The plot id that needs to be deleted",
         *     required=true,
         *     type="string"
         *   ),
         *   @SWG\Parameter(
         *     name="password",
         *     in="formData",
         *     description="The plot password",
         *     required=true,
         *     type="string"
         *   ),
         *   @SWG\Response(response=422,  @SWG\Schema(ref="#/definitions/error"), description="Invalid password"),
         *   @SWG\Response(response=404,  @SWG\Schema(ref="#/definitions/error"), description="Plot not found")
         * )
         */
        $app->delete('{id}', 'PlotsController@delete');

        /**
         * @SWG\Post(path="/plots/{id}",
         *   tags={"plots"},
         *   summary="Update plot",
         *   description="This endpoint updates the plot fields.",
         *   operationId="updatePlot",
         *   consumes={"multipart/form-data"},
         *   produces={"application/json"},
         *   @SWG\Parameter(
         *     name="id",
         *     in="path",
         *     description="id of the plot",
         *     required=true,
         *     type="string"
         *   ),
         *   @SWG\Parameter(
         *     name="name",
         *     in="formData",
         *     description="Plot name",
         *     required=true,
         *     type="string"
         *   ),
         *   @SWG\Parameter(
         *     name="password",
         *     in="formData",
         *     description="The plot password",
         *     required=true,
         *     type="string"
         *   ),
         *   @SWG\Response(response=422, @SWG\Schema(ref="#/definitions/error"), description="Invalid plot supplied"),
         *   @SWG\Response(response=404, @SWG\Schema(ref="#/definitions/error"), description="Plot not found")
         * )
         */
        $app->post('{id}', 'PlotsController@update');

        /**
         * @SWG\Get(
         *     path="/plots/{id}",
         *     summary="Get plot fields by id",
         *     tags={"plots"},
         *     description="This endpoint returns plot fields for a given id.",
         *     operationId="getPlotById",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *   @SWG\Parameter(
         *     name="id",
         *     in="path",
         *     description="The plot id that needs to be matched",
         *     required=true,
         *     type="integer"
         *   ),
         *     @SWG\Response(
         *         response=200,
         *         description="Plot object",
         *         @SWG\Schema(
         *             ref="#/definitions/plot"
         *         )
         *     ),
         *     @SWG\Response(response=404, @SWG\Schema(ref="#/definitions/error"), description="Plot not found")
         * )
         */
        $app->get('{id}', 'PlotsController@find');

        /**
         * @SWG\Post(
         *     path="/plots/{id}/script",
         *     summary="Get the script of the plot",
         *     tags={"plots"},
         *     description="Get the url of the script that is used for the plot",
         *     operationId="plotScript",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *   @SWG\Parameter(
         *     name="id",
         *     in="path",
         *     description="The plot id",
         *     required=true,
         *     type="integer"
         *   ),
         *     @SWG\Response(
         *         response=200,
         *         description="Script url",
         *         @SWG\Schema(
         *             ref="#/definitions/url"
         *         )
         *     ),
         *     @SWG\Response(response=404, @SWG\Schema(ref="#/definitions/error"), description="Plot not found")
         * )
         */
        $app->post('{id}/script', 'PlotsController@script');
    });

    $app->group(['prefix' => 'templates'], function ($app) {
        /**
         * @SWG\Get(
         *     path="/templates",
         *     summary="Get the list of the templates",
         *     tags={"templates"},
         *     description="This endpoint returns a list of the templates containing all the fields",
         *     operationId="allTemplates",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *     @SWG\Response(
         *         response=200,
         *         description="template response",
         *         @SWG\Schema(
         *             type="array",
         *             @SWG\Items(ref="#/definitions/template")
         *         )
         *     )
         * )
         */
        $app->get('/', 'TemplatesController@all');

        /**
         * @SWG\Get(
         *     path="/templates/{id}",
         *     summary="Get plot fields by id",
         *     tags={"templates"},
         *     description="This endpoint returns template fields for a given id.",
         *     operationId="getTemplateById",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *   @SWG\Parameter(
         *     name="id",
         *     in="path",
         *     description="The template id that needs to be matched",
         *     required=true,
         *     type="integer"
         *   ),
         *     @SWG\Response(
         *         response=200,
         *         description="Plot object",
         *         @SWG\Schema(
         *             ref="#/definitions/template"
         *         )
         *     ),
         *     @SWG\Response(response=404, @SWG\Schema(ref="#/definitions/error"), description="Template not found")
         * )
         */
        $app->get('{id}', 'TemplatesController@find');

        /**
         * @SWG\Post(
         *     path="/templates/{id}/preview",
         *     summary="Generate template preview",
         *     operationId="preview",
         *     tags={"templates"},
         *     description="Generate a preview for a given template and dataset",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *   @SWG\Parameter(
         *      in="path",
         *      name="id",
         *      description="Template id",
         *      required=true,
         *      type="integer"
         *     ),
         *   @SWG\Parameter(
         *     name="data",
         *     in="formData",
         *     description="Data file",
         *     required=true,
         *     type="file"
         *   ),
         *     @SWG\Response(
         *         response=200,
         *         description="Image object",
         *         @SWG\Schema(ref="#/definitions/image")
         *     ),
         *     @SWG\Response(response=422, @SWG\Schema(ref="#/definitions/error"), description="Invalid data"),
         *     @SWG\Response(response=404, @SWG\Schema(ref="#/definitions/error"), description="Template not found")
         * )
         */
        $app->post('{id}/preview', 'TemplatesController@preview');

        /**
         * @SWG\Post(
         *     path="/templates/{id}/script",
         *     summary="Download template script",
         *     operationId="script",
         *     tags={"templates"},
         *     description="Download the script used for that template",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         *   @SWG\Parameter(
         *      in="path",
         *      name="id",
         *      description="Template id",
         *      required=true,
         *      type="integer"
         *     ),
         *     @SWG\Response(
         *         response=200,
         *         description="Image object",
         *         @SWG\Schema(ref="#/definitions/image")
         *     ),
         *     @SWG\Response(response=404, @SWG\Schema(ref="#/definitions/error"), description="Template not found")
         * )
         */
        $app->post('{id}/script', 'TemplatesController@script');

    });
});

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    $app->get('docs', function () {
        return view('swagger');
    });
});

