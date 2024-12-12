<?php

namespace Alex\TaskManagerApp\Entities;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}