<style>
html {
  font-size: 12px;
}

table {
  border-collapse:collapse;
  font-size: 100%;
}
td, th {
  border: 1px solid #000000;
  padding: 0.1em 0.5em;
}
ul {
  margin: 0;
  padding: 0;
  list-style-type: none;
}
</style>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Method</th>
      <th>URI</th>
      <th>Middleware</th>
    </tr>
  </thead>
  <tbody>
      @foreach($routes as $route)
        <tr>
          <td>
            @if(in_array('GET', $route['methods']) && empty($route['parameters']))
              <a href="{{ route($route['name']) }}">{{ $route['name'] }}</a>
            @else
              {{ $route['name'] }}
            @endif
          </td>
          <td><ul>
            @foreach($route['methods'] as $method)
              <li>{{ $method }}</li>
            @endforeach
          </ul> </td>
          <td>{{ $route['uri'] }}</td>
          <td><ul>
            @foreach($route['middleware'] as $middleware)
              <li>{{ $middleware }}</li>
            @endforeach
          </ul> </td>
        </tr>
      @endforeach
  </tbody>
</table>
