@props(['rating', 'maxStars' => 5, 'showNumber' => true, 'comment' => null])

<div class="flex items-center space-x-1">
    <div class="flex">
        @for($i = 1; $i <= $maxStars; $i++)
            @if($i <= $rating)
                <i class="fas fa-star text-yellow-400"></i>
            @else
                <i class="far fa-star text-yellow-400"></i>
            @endif
        @endfor
    </div>
    
    @if($showNumber)
        <span class="text-sm text-gray-600">({{ number_format($rating, 1) }})</span>
    @endif
    
    @if($comment)
        <div class="text-xs text-gray-500 mt-1" title="{{ $comment }}">
            "{{ Str::limit($comment, 50) }}"
        </div>
    @endif
</div>
