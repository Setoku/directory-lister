<?php

// function to get the file extension (type)
function ext( $file ) {
    if ( is_dir( $file ) ) {
        return 'dir';
    } else {
        return str_replace( '7z', 'sevenz', strtolower( pathinfo( $file )['extension'] ) );
    }
}

// function to get the title, from the url
function title() {
    $url = substr( $_SERVER['REQUEST_URI'], 1 );
    if ( empty( $url ) ) $url = 'home/';
    return $url;
}

// function to get human-readable filesize
function human_filesize( $file ) {
    $bytes = filesize( $file );
    $decimals = 1;
    $factor = floor( ( strlen($bytes) - 1 ) / 3 );
    if ( $factor > 0 ) $sz = 'KMGT';
    return sprintf( "%.{$decimals}f", $bytes / pow( 1024, $factor ) ) . @$sz[$factor - 1] . 'B';
}

// get the file list for the current directory
$files = scandir( '.' );

// files to exclude from the files array.
$exclude = array( '.', '..', '.DS_Store', 'index.php', '.git', '.gitmodules', '.gitignore', 'node_modules' );

// search files array and remove anything in the exclude array
foreach ( $exclude as $ex ) {
    if ( ( $key = array_search( $ex, $files ) ) !== false ) {
        unset( $files[$key] );
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= str_replace('/', ' / ', htmlspecialchars(title())) ?></title>

    <style>
        @import url('https://fonts.googleapis.com/css?family=Raleway:400,500,600&display=swap');

        :root {
            --bg:        #0f1117;
            --bg-card:    #161b22;
            --text:       #e6edf3;
            --text-dim:   #8b949e;
            --border:     #30363d;
            --accent:     #58a6ff;
            --accent-dim: #388bfd;
            --hover:      #21262d;
            --shadow:     0 4px 20px rgba(0,0,0,0.4);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Raleway', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            min-height: 100vh;
        }

        .container {
            max-width: 860px;
            margin: 40px auto;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        h1 {
            margin: 0;
            padding: 1.1rem 1.4rem;
            font-size: 1.35rem;
            font-weight: 500;
            background: #0d1117;
            border-bottom: 1px solid var(--border);
            color: #c9d1d9;
        }

        h1 span {
            color: #444c56;
            font-weight: 400;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0.4rem 0 1.2rem;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            display: block;
        }

        li {
            padding: 0.65rem 1.4rem 0.65rem 2.6rem;
            background-position: 12px center;
            background-repeat: no-repeat;
            background-size: 18px;
            transition: all 0.14s ease;
        }

        li:hover {
            background-color: var(--hover);
            color: #ffffff;
        }

        li span {
            color: var(--text-dim);
            font-size: 0.82rem;
            margin-left: 0.6rem;
        }

        p.empty {
            padding: 2rem;
            text-align: center;
            color: var(--text-dim);
            font-size: 1.05rem;
        }

        /* File type icons — only changing background color slightly for dark mode */
        ul li.dir       { background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGrSURBVDjLxZO7ihRBFIa/6u0ZW7GHBUV0UQQTZzd3QdhMQxOfwMRXEANBMNQX0MzAzFAwEzHwARbNFDdwEd31Mj3X7a6uOr9BtzNjYjKBJ6nicP7v3KqcJFaxhBVtZUAK8OHlld2st7Xl3DJPVONP+zEUV4HqL5UDYHr5xvuQAjgl/Qs7TzvOOVAjxjlC+ePSwe6DfbVegLVuT4r14eTr6zvA8xSAoBLzx6pvj4l+DZIezuVkG9fY2H7YRQIMZIBwycmzH1/s3F8AapfIPNF3kQk7+kw9PWBy+IZOdg5Ug3mkAATy/t0usovzGeCUWTjCz0B+Sj0ekfdvkZ3abBv+U4GaCtJ1iEm6ANQJ6fEzrG/engcKw/wXQvEKxSEKQxRGKE7Izt+DSiwBJMUSm71rguMYhQKrBygOIRStf4TiFFRBvbRGKiQLWP29yRSHKBTtfdBmHs0BUpgvtgF4yRFR+NUKi0XZcYjCeCG2smkzLAHkbRBmP0/Uk26O5YnUActBp1GsAI+S5nRJJJal5K1aAMrq0d6Tm9uI6zjyf75dAe6tx/SsWeD//o2/Ab6IH3/h25pOAAAAAElFTkSuQmCC"); }
        /* You can keep most of your original icon base64 — they work fine on dark bg */

        @media (max-width: 850px) {
            .container {
                margin: 16px;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?= str_replace('/', ' <span>/</span> ', htmlspecialchars(title())) ?></h1>

    <?php if ( !empty($files) ): ?>
        <ul>
        <?php foreach ( $files as $file ): ?>
            <a href="./<?= htmlspecialchars($file) ?>">
                <li class="<?= ext($file) ?>">
                    <?= htmlspecialchars($file) ?>
                    <?php if ( !is_dir($file) ): ?>
                        <span><?= human_filesize($file) ?></span>
                    <?php endif; ?>
                </li>
            </a>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="empty">This folder contains no files.</p>
    <?php endif; ?>
</div>

</body>
</html>
