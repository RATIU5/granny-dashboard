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
			padding: 0.5rem;
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
			-webkit-touch-callout: none;
			/* iOS Safari */
			-webkit-user-select: none;
			/* Safari */
			-khtml-user-select: none;
			/* Konqueror HTML */
			-moz-user-select: none;
			/* Old versions of Firefox */
			-ms-user-select: none;
			/* Internet Explorer/Edge */
			user-select: none;
			/* Non-prefixed version, currently
                                  supported by Chrome, Edge, Opera and Firefox */
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

		.li-branch {
			margin: 0.5rem 0;
			cursor: pointer;
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
			cursor: pointer;
		}

		.li-branch details .ul-tree {
			margin-left: 1.2rem;
		}

		details summary p {
			margin: 0;
			display: inline;
		}

		@media (prefers-color-scheme: dark) {
			body {
				color: #DEDEDE;
				background-color: #222A33;
			}

			nav ul li a {
				color: #888990;
			}

			nav ul li a:hover {
				color: #C8C9D0;
			}

			.li-branch p a {
				color: #DEDEDE;
				border-bottom: solid 1px #DEDEDE;
			}

			.li-branch p a::after {
				text-decoration: none;
				content: " ↗︎";
				font-size: 0.9rem;
				border-bottom: solid 3px #29303B;
			}

			.directory-container {
				background: #29303B;
			}

			.li-branch.branch-empty {
				color: #888990;
			}

			.title img {
				background: white;
			}
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
							define("WD", "/htdocs/www");

							$IGNORED_FILES = [".", "..", ".DS_Store", "Dockerfile", "docker-compose.yml", "docker-entrypoint.sh"];

							function listFolderFiles($dir)
							{
								// Array of files and folders
								$fs = scandir($dir);

								// Remove ignored files
								$ffs = array_diff($fs, $GLOBALS['IGNORED_FILES']);

								// Display empty folder
								if (count($ffs) < 1) {
									echo '<li class="li-branch file branch-empty"><p>No projects!</p></li>';
									return;
								}

								foreach ($ffs as $ff) {
									$path = $dir  . "/" . $ff;
									// Retrieve the docker working directory for php
									$url = 'http://localhost/www' . substr($path, strlen(WD), strlen($path));

									if (is_dir($path)) {
										echo '<li class="tree-container li-branch"><details><summary><p>' . $ff . '</p></summary><ul class="ul-tree">';
										listFolderFiles($path);
										echo '</ul></details></li>';
									} else {
										echo '<li class="li-branch file"><p><a href="' . $url . '" target="_blank">' . $ff . '</a></p></li>';
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
			<p>Copyright &copy; Granny Software & Co</p>
		</footer>
	</div>

</body>

</html>
