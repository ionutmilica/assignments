<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="images/favicon-16x16.png" sizes="16x16" />
    <link href='assets/swagger/css/typography.css' media='screen' rel='stylesheet' type='text/css'/>
    <link href='assets/swagger/css/reset.css' media='screen' rel='stylesheet' type='text/css'/>
    <link href='assets/swagger/css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
    <link href='assets/swagger/css/reset.css' media='print' rel='stylesheet' type='text/css'/>
    <link href='assets/swagger/css/print.css' media='print' rel='stylesheet' type='text/css'/>
    <script src='assets/swagger/lib/jquery-1.8.0.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/jquery.slideto.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/jquery.wiggle.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/jquery.ba-bbq.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/handlebars-2.0.0.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/js-yaml.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/lodash.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/backbone-min.js' type='text/javascript'></script>
    <script src='assets/swagger/swagger-ui.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/highlight.9.1.0.pack.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/highlight.9.1.0.pack_extended.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/jsoneditor.min.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/marked.js' type='text/javascript'></script>
    <script src='assets/swagger/lib/swagger-oauth.js' type='text/javascript'></script>

    <!-- <script src='assets/swagger/lang/translator.js' type='text/javascript'></script> -->
    <!-- <script src='assets/swagger/lang/en.js' type='text/javascript'></script> -->

    <script type="text/javascript">
        $(function () {
            var url = window.location.search.match(/url=([^&]+)/);
            if (url && url.length > 1) {
                url = decodeURIComponent(url[1]);
            } else {
                url = "{{ route('api.swagger') }}";
            }

            hljs.configure({
                highlightSizeThreshold: 5000
            });

            // Pre load translate...
            if(window.SwaggerTranslator) {
                window.SwaggerTranslator.translate();
            }
            window.swaggerUi = new SwaggerUi({
                url: url,
                dom_id: "swagger-ui-container",
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                onComplete: function(swaggerApi, swaggerUi){
                    if(window.SwaggerTranslator) {
                        window.SwaggerTranslator.translate();
                    }
                },
                onFailure: function(data) {
                    log("Unable to Load SwaggerUI");
                },
                docExpansion: "none",
                jsonEditor: false,
                defaultModelRendering: 'schema',
                showRequestHeaders: false
            });

            window.swaggerUi.load();

            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
        });
    </script>
</head>

<body class="swagger-section">
<div id='header'>
    <div class="swagger-ui-wrap">
        <a id="logo" href="http://swagger.io"><img class="logo__img" alt="swagger" height="30" width="30" src="assets/swagger/images/logo_small.png" />
        </a>
    </div>
</div>

<div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</body>
</html>
