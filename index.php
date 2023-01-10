<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Granny Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            padding: 0;
            margin: 0;
            font-size: 18px;
            color: #333333;
            background-color: #FDFDFD;
            font-family: Arial, Helvetica, sans-serif;
        }
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 100;
        }
        h1 {
            font-size: 2.5rem;
        }
        h2 {
            font-size: 1.75rem;
        }
        h3 {
            font-size: 1.25rem;
        }
        h4 {
            font-size: 1.05rem;
        }
        p {
            line-height: 1.75rem;
            letter-spacing: 0.05rem;
        }
        .container {
            display: flex;
            flex-wrap: nowrap;
            flex-direction: column;
            justify-content: center;
            height: 100vh;
        }
        header,
        footer {
            flex-grow: 0;
        }
        main {
            flex-grow: 1;
        }
        header {
            position: sticky;
            display: flex;
            padding: 1rem;
            margin: 0 auto;
            justify-content: space-between;
            align-items: center;
            max-width: 50rem;
            width: 100%;
        }
        nav ul li a {
            text-decoration: none;
            color: #888888;
        }
        nav ul li a:hover {
            color: #222222;
        }
        nav ul li {
            list-style-type: none;
            margin: 0 0.75rem;
            display: inline-block;
        }
        nav ul li:last-child {
            margin: 0 0 0 0.75rem;
            display: inline-block;
        }
        .title {
            font-weight: 100;
        }
        .title img {
            display: inline-block;
            height: 5rem;
            margin: 0;
            padding: 0;
            z-index: 1;
        }
        footer p {
            text-align: center;
        }
        .content {
            padding: 0 1rem;
            margin: 6rem auto 0 auto;
            max-width: 100ch;
        }
        .ul-tree,
        .li-branch {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .directory-container {
            max-height: 50rem;
            overflow: auto;
            border-radius: 0.25rem;
            background: #F5F5F5;
            padding: 1rem;
        }
        .li-branch.file p {
            margin: 0 0 0 1.2rem;
        }
        .li-branch.branch-empty {
            font-style: italic;
            color: #BEBEBE;
            border-color: silver;
        }
        .li-branch p a {
            color: #333333;
            text-decoration: none;
            border-bottom: solid 1px #333333;
        }
        .li-branch p a::after {
            text-decoration: none;
            content: " ↗︎";
            font-size: 0.9rem;
            border-bottom: solid 3px #F5F5F5;
        }
        details summary {
            cursor: default;
        }
        .li-branch details .ul-tree {
            margin-left: 1.2rem;
        }
        details summary p {
            margin: 0;
            display: inline;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="title">
                <img src="./public/granny.png" alt='Granny' title="Granny" />
            </div>
            <nav>
                <ul>
                    <li><a href="/">Dashboard</a></li>
                    <li><a href="/phpmyadmin">phpMyAdmin</a></li>
                    <li><a href="/phpinfo">PHP Info</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <section>
                <div class="content">
                    <h2>File System</h2>
                    <div class="directory-container">

                        <ul class="ul-tree">
                            <?php
                            define("WD", "/var/www/localhost/htdocs/www");

                            function listFolderFiles($dir)
                            {
                                // Array of files and folders
                                $ffs = scandir($dir);

                                // Remove the dot directories
                                unset($ffs[array_search('.', $ffs, true)]);
                                unset($ffs[array_search('..', $ffs, true)]);

                                // Display empty folder
                                if (count($ffs) < 1) {
                                    echo '<li class="li-branch file branch-empty"><p>No projects!</p></li>';
                                    return;
                                }

                                foreach ($ffs as $ff) {
                                    $path = $dir  . "/" . $ff;
                                    // Retrieve the docker working directory for php
                                    $url = 'http://localhost/www' . substr($path, strlen(WD), strlen($path));

                                    if (is_link($path)) {
                                        echo '<li class="tree-container li-branch"><details><summary><p>' . $ff . '</p></summary><ul class="ul-tree">';
                                        listFolderFiles($path);
                                        echo '</ul></details></li>';
                                    } else {
                                        echo '<li class="li-branch file"><p><a href="' . $url . '">' . $ff . ': ' . filetype($path) . '</a></p></li>';
                                    }
                                }
                            }

                            // List the current directory on the server
                            listFolderFiles(WD);
                            ?>
                        </ul>

                    </div>
                </div>
            </section>
        </main>
        <footer>
            <p>Copyright &copy; Granny LLC</p>
        </footer>
    </div>

</body>

</html>
