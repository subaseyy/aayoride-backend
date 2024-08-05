<?php
$color = businessConfig('website_color')?->value;
$text = businessConfig('text_color')?->value
?>


@if(isset($color))

<style>
    :root {
        --text-primary: {{$color['primary'] ?? '#14B19E'}};
        --text-secondary: {{$color['secondary'] ?? '#D7F9F5'}};
        --bs-body-bg: {{$color['background'] ?? '#F4FCFB'}} ;
        --bs-primary: {{$color['primary'] ?? '#14B19E'}};
        --bs-secondary-rgb: {{$color['secondary'] ?? '#D7F9F5'}};
        --bs-secondary: {{$color['secondary'] ?? '#D7F9F5'}};
    }
</style>

@endif

@if(isset($text))
    <style>
        :root {
            --title-color: {{$text['primary'] ?? '#293231'}};
            --title-color-rgb: {{$text['secondary'] ?? '41, 50, 49'}};
        }
    </style>
@endif
