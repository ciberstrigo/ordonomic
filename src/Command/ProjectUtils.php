<?php

namespace Jegulnomic\Command;

use Jegulnomic\Systems\Command;

class ProjectUtils extends AbstractCommand
{
    public function sizeMB(): void
    {
        $currentBranch = exec('git rev-parse --abbrev-ref HEAD');
        exec('git ls-tree -r ' . $currentBranch . ' --name-only', $output);

        $result = 0;

        foreach ($output as $file) {
            $result += $this->fileSize($file);
            Command::output($file);
        }

        $result += $this->folderSize(ENTRYPOINT_DIR . '/../vendor');
        Command::output('Total (MB)' . number_format($result / 1024 / 1024, 2));
    }

    public function env(): void
    {
        Command::output($_ENV['APP_ENV']);
    }

    private function folderSize($dir): int
    {
        $size = 0;

        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }

        return $size;
    }

    private function fileSize($file): int
    {
        return is_file($file) ? filesize($file) : 0;
    }
}
