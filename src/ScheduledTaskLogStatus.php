<?php

namespace AdvSorcer\FilamentScheduleUI;

enum ScheduledTaskLogStatus: string
{
    case Running = 'running';
    case Success = 'success';
    case Failed = 'failed';
    case Skipped = 'skipped';
}

