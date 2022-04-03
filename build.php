nodb-forum-pd-help build tool
This will generate a base64-encoded
subpage file that can be copied into
a user account.

First, let's fill in some basic information.
The current directory is:
<?php echo getcwd(); ?>

Where do you want to save the file?
<?php $filename = readline('File name: '); ?>

Saving to <?php echo $filename; ?>.

Building from <?php echo realpath("content/"); ?>.

<?php
function getSubpages(string $path = "content", int $indent = 0) {
    $spaces = str_repeat(" ", $indent);
    $tabSize = 2;
    echo $spaces . "Adding subpages from $path";
    $object = new stdClass;
    $object->files = new stdClass;
    $object->folders = new stdClass;
    $object->title = "Site Help";
    $object->type = "folder";
    $files = array_diff(scandir($path, SCANDIR_SORT_NONE), array('.', '..'));
    foreach ($files as $file) {
        if (is_dir("$path/$file")) {
            echo $spaces . "Adding folder $file";
            $object->folders->$file = getSubpages("$path/$file", $indent + $tabSize);
        } else {
            echo $spaces . "Adding page $file";
            $page = new stdClass;
            $ext = pathinfo("$path/$file", PATHINFO_EXTENSION);
            $page->contentType = $ext;
            $page->title = pathinfo("$path/$file", PATHINFO_FILENAME);
            $page->type = "page";
            $page->contents = file_get_contents("$path/$file");
            $firstrev = new stdClass;
            $firstrev->time = time();
            $firstrev->contents = $page->contents;
            $firstrev->summary = "Building help from nodb-forum-pd-help";
            $revs = array($firstrev);
            $page->revisions = $revs;
            $object->files->{pathinfo("$path/$file", PATHINFO_FILENAME)} = $page;
        }
    }
    return $object;
}
$sub = getSubpages();
fwrite(fopen($filename, "w+"), base64_encode(serialize($sub)));
?>