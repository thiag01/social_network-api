<h1> {{$msg[0]}} {{$msg[2]}} </h1>

<div>
    <a href="{{route('edit')}}"> Editar || </a>
    <a href="{{route('remove')}}"> Remover || </a>


    <!-- <a href="{{route('remove')}}"> Remover || </a> passando dados atraves da rota link -->
</div>
@foreach($list as $item)
    <li> {{$item->nome}}</li>
@endforeach
