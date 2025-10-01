<?php

namespace App\Services;

use App\Models\Cctv;
use Symfony\Component\Process\Process;

class FfmpegStreamService
{
    public function start(Cctv $cctv): void
    {
        $streamKey = 'cam'.str_pad((string)$cctv->id, 3, '0', STR_PAD_LEFT);
        $outputDir = public_path("live/{$streamKey}");
        @mkdir($outputDir, 0775, true);
        $output = $outputDir.'/index.m3u8';

        $input = $cctv->ip_rtsp;
        $cmd = [
            'ffmpeg', '-nostdin', '-rtsp_transport', 'tcp', '-i', $input,
            '-fflags', 'flush_packets', '-max_delay', '5', '-flags', '-global_header',
            '-hls_time', '2', '-hls_list_size', '3', '-hls_flags', 'delete_segments+append_list',
            '-vcodec', 'copy', '-acodec', 'aac', '-f', 'hls', $output
        ];

        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->disableOutput();
        $process->start();

        $cctv->update([
            'hls_path' => 'live/'.$streamKey.'/index.m3u8',
            'ffmpeg_pid' => $process->getPid(),
            'status' => 'online',
        ]);
    }

    public function stop(Cctv $cctv): void
    {
        if ($cctv->ffmpeg_pid) {
            @posix_kill($cctv->ffmpeg_pid, SIGTERM);
        }
        $cctv->update(['status' => 'offline', 'ffmpeg_pid' => null]);
    }
}

