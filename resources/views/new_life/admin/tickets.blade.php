{!! Form::open(["url" => route('ticket.store'), 'method' => "POST", "class" => "box-typical"]) !!}
<input name="title" type="text" class="write-something" placeholder="Заголовок тикета"/>
<textarea name="text" class="write-something" placeholder="Текст тикета"></textarea>
<div class="box-typical-footer">
    <div class="tbl">
        <div class="tbl-row">
            <div class="tbl-cell">
            </div>
            <div class="tbl-cell tbl-cell-action">
                <button type="submit" class="btn btn-rounded">Добавить</button>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
@foreach($tickets as $ticket)
    <section class="box-typical">
        <header class="box-typical-header-sm">{{ $ticket->title }}</header>
        <article class="profile-info-item">
            <header class="profile-info-item-header">
                <i class="font-icon font-icon-notebook-bird"></i>
                {{ $ticket->created_at }}
            </header>
            <div class="text-block text-block-typical">
                <p>{{ $ticket->text }}</p>
            </div>
        </article><!--.profile-info-item-->
        {!! Form::open(["url" => route('ticket.destroy', ["id" => $ticket->id]), 'method' => "POST", "id" => "newDelete"]) !!}
        {!! Form::button('Удалить', ['class' => 'btn btn-danger news-delete','type'=>'submit']) !!}
        {!! Form::hidden('_method', "DELETE") !!}
        {!! Form::close() !!}
    </section>
@endforeach