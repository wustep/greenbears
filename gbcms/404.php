<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Page Not Found :(</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
		<base href="<?php require_once("config.php"); echo $url; ?>">
        <style>
            ::-moz-selection {
                background: #b3d4fc;
                text-shadow: none;
            }

            ::selection {
                background: #b3d4fc;
                text-shadow: none;
            }

            html {
                padding: 10px 10px;
                font-size: 18px;
                line-height: 1.3;
                color: #3C3C3C;
                background: #EDF7E7;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
            }

            html,
            input {
				font-family: "Helvetica Neue", Helvetica, Verdana, Arial, sans-serif;
            }

            body {
                max-width: 500px;
                _width: 500px;
                padding: 20px 20px 30px;
                border: 1px solid #284819;
                border-radius: 4px;
                margin: 0 auto;
                background: #fcfcfc;
				background-color: #BEDEAF;
            }

            h1 {
                margin: 15px 0 5px 0;
                font-size: 40px;
                text-align: center;
            }

            h1 span {
                color: #6E6E6E;
            }

            h3 {
                margin: 1em 0 0.5em;
            }

            p {
                margin: 1em 0;
            }

            ul {
                padding: 0 0 0 40px;
                margin: 10px 0;
            }
			
			.or {
				padding-left: 60px;
			}

            .container {
                max-width: 380px;
                _width: 380px;
                margin: 0 auto;
            }

            /* google search */

            #goog-fixurl ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            #goog-fixurl form {
                margin: 0;
            }

            #goog-wm-qt,
            #goog-wm-sb {
                border: 1px solid #bbb;
                font-size: 15px;
                line-height: normal;
                vertical-align: top;
                color: #444;
                border-radius: 2px;
            }

            #goog-wm-qt {
                width: 220px;
                height: 20px;
                padding: 5px;
                margin: 5px 10px 0 0;
                box-shadow: inset 0 1px 1px #ccc;
            }

            #goog-wm-sb {
                display: inline-block;
                height: 32px;
                padding: 0 10px;
                margin: 5px 0 0;
                white-space: nowrap;
                cursor: pointer;
                background-color: #f5f5f5;
                background-image: -webkit-linear-gradient(rgba(255,255,255,0), #f1f1f1);
                background-image: -moz-linear-gradient(rgba(255,255,255,0), #f1f1f1);
                background-image: -ms-linear-gradient(rgba(255,255,255,0), #f1f1f1);
                background-image: -o-linear-gradient(rgba(255,255,255,0), #f1f1f1);
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                *overflow: visible;
                *display: inline;
                *zoom: 1;
            }

            #goog-wm-sb:hover,
            #goog-wm-sb:focus {
                border-color: #aaa;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                background-color: #f8f8f8;
            }

            #goog-wm-qt:hover,
            #goog-wm-qt:focus {
                border-color: #105cb6;
                outline: 0;
                color: #222;
            }

            input::-moz-focus-inner {
                padding: 0;
                border: 0;
            }
			@media only screen and (max-width: 500px) {
				body, #container {
					width: auto;
					margin: 0 auto;
					border: 0;
				}
				html {
					padding: 0;
				}
			}
        </style>
    </head>
    <body>
        <div class="container">
			<img src="img/hedgehog.png" style="width: 236px; height: 262px; margin: 0 auto; display: block;"/>
            <h1>Not found <span>:(</span></h1>
            <p>Sorry, but the page you were trying to view does not exist.</p>
            <p>This may have been the result of either:</p>
            <ul>
                <li>a mistyped address</li>
				<span class="or">or</span>
                <li>an outdated link</li>
            </ul>
            <script>
                var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),GOOG_FIXURL_SITE = location.host;
            </script>
            <script src="//linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>
        </div>
    </body>
</html>