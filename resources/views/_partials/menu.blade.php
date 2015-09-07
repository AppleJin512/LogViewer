<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-fw fa-flag"></i> Levels</div>
    <ul class="list-group">
        @foreach($menu as $level => $item)
            @if ($item['count'] === 0)
                <a href="#" class="list-group-item disabled">
                    <span class="badge">{{ $item['count'] }}</span>
                    {!! log_styler()->icon($level) !!} {{ $item['name'] }}
                </a>
            @else
                <a href="{{ route('log-viewer::logs.filter', [$date, $level]) }}" class="list-group-item {{ $level }}">
                    <span class="badge level-{{ $level }}">
                        {{ $item['count'] }}
                    </span>
                    <span class="level level-{{ $level }}">
                        {!! log_styler()->icon($level) !!} {{ $item['name'] }}
                    </span>
                </a>
            @endif
        @endforeach
    </ul>
</div>
