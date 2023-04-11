# dtable
Data Table for Laravel 8.

```
if (request()->ajax()) {
    $columns = array(
        0 => 'users.id',
        1 => 'users.picture',
        2 => 'users.username',
        3 => 'users.name',
        4 => 'users.email',
        5 => 'role.name',
    );

    $data = User::join('role', 'role.id', '=', 'users.role_id');
    $results = DataTable::results($columns, $data, $request);
    foreach ($results as $item) {
        $user = new User();
        $item->action = DataTable::addAction($item, $columns, $user->getTable());
        $item->users_picture = $item->picture != null ? '<img class="profile-user-img img-fluid img-circle" src="https://siakad.dalwa.ac.id/picture_users/"' . $item->picture . ' style="width: 50px;height:50px;object-fit:cover" alt="User profile picture"/>' : '<img src="https://picsum.photos/100" class="img-circle elevation-2" style="width: 50px;height:50px;object-fit:cover" alt="User Image">';
    }

    $resultFiltered = DataTable::resultsFiltered($columns, $data, $request);
    return DataTable::responseDataTable($results, $resultFiltered, User::count());
}
```

Feel Free to use.