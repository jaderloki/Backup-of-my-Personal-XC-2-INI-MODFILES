<?php
date_default_timezone_set('America/Sao_Paulo');

function copyDirectory($source, $destination) {
    if (!is_dir($source)) {
        echo "Source directory does not exist.";
        return false;
    }

    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $dir = opendir($source);
    while(($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        
        $srcFile = $source . '/' . $file;
        $destFile = $destination . '/' . $file;

        if (is_dir($srcFile)) {
            copyDirectory($srcFile, $destFile);
        } else {
            copy($srcFile, $destFile);
        }
    }

    closedir($dir);
    return true;
}

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
	$final = array();
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
		$final = array_merge($final, rglob($dir . "/" . basename($pattern), $flags));
		if(basename($dir) == 'Config'){
			$final[] = $dir;
		}
    }
    return $final;
}

$rootDirectory = 'C:\Program Files (x86)\Steam\steamapps\workshop\content\268500\\';

$destinationDirectory = __DIR__ . '/../destino '.date('Y-m-d His').'/';
$destinationDirectoryGITHUB = __DIR__ . '/ini/';

deleteFolderContents($destinationDirectoryGITHUB);

// Executa a função para encontrar e copiar as pastas 'config'
$aa = rglob($rootDirectory);
echo '<pre>'.print_r($aa, true).'</pre>';
if($aa != null){
	foreach($aa as $bb){
		$cc = str_replace('C:\Program Files (x86)\Steam\steamapps\workshop\content/268500/', '', $bb);
		var_dump($cc);
		recurseCopy ($bb, $destinationDirectory.$cc);
		recurseCopy ($bb, $destinationDirectoryGITHUB.$cc);
	}
}



function recurseCopy(
    string $sourceDirectory,
    string $destinationDirectory,
    string $childFolder = ''
): void {
    $directory = opendir($sourceDirectory);

    if (is_dir($destinationDirectory) === false) {
        mkdir($destinationDirectory, 777, true);
    }

    if ($childFolder !== '') {
        if (is_dir("$destinationDirectory/$childFolder") === false) {
            mkdir("$destinationDirectory/$childFolder", 777, true);
        }

        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_dir("$sourceDirectory/$file") === true) {
                recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
            } else {
                copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
            }
        }

        closedir($directory);

        return;
    }

    while (($file = readdir($directory)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (is_dir("$sourceDirectory/$file") === true) {
            recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
        }
        else {
            copy("$sourceDirectory/$file", "$destinationDirectory/$file");
        }
    }

    closedir($directory);
}

function deleteFolderContents($folder) {
    if (!is_dir($folder)) {
        return false;
    }

    $files = array_diff(scandir($folder), ['.', '..']); // ignora . e ..

    foreach ($files as $file) {
        $path = $folder . DIRECTORY_SEPARATOR . $file;

        if (is_dir($path)) {
            // recursivo para subpastas
            deleteFolderContents($path);
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    return true;
}