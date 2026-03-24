<x-mail::message>
# Automated Report: {{ $view->name }} (v{{ $view->version }})

Hello, this is your scheduled report based on your saved dashboard view.

<x-mail::table>
| {{ implode(' | ', array_slice(array_keys($docs[0] ?? ['No Data' => '']), 0, 4)) }} |
| :--- | :--- | :--- | :--- |
@foreach(array_slice($docs, 0, 10) as $doc)
| {{ @implode(' | ', array_slice(array_values($doc), 0, 4)) }} |
@endforeach
</x-mail::table>

@if(count($docs) > 10)
*... and {{ count($docs) - 10 }} more rows.*
@endif

<x-mail::button :url="config('app.url') . '/reports'">
View Full Report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} team
</x-mail::message>
