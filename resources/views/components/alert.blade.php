@props(['type','message'])


<div {{$attributes->merge(['class'=>'container'])->class(['mb-5','text'])}}>
    <div
        class="alert alert-{{$type}}"
        role="alert"
    >
        <strong>{{$message}}</strong>
        {{$slot}}
    </div>

</div>
