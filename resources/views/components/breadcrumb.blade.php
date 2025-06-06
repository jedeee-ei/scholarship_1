@props(['items' => []])

<nav class="breadcrumb-nav" aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if (!empty($items))
            <li class="breadcrumb-separator">
                <i class="fas fa-chevron-right"></i>
            </li>
        @endif

        @foreach ($items as $index => $item)
            @if ($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    @if (isset($item['icon']))
                        <i class="{{ $item['icon'] }}"></i>
                    @endif
                    <span>{{ $item['title'] }}</span>
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">
                        @if (isset($item['icon']))
                            <i class="{{ $item['icon'] }}"></i>
                        @endif
                        <span>{{ $item['title'] }}</span>
                    </a>
                </li>
                <li class="breadcrumb-separator">
                    <i class="fas fa-chevron-right"></i>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
