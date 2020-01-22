<div class="toolkit">
    <p>
        Пользователи
    </p>
    <script>$("title").text("Пользователи"); </script>
</div>
<div class="">



    <div class="form-group" style="margin-bottom: 25px;">

        <div class="btn-group">
            <div class="dropdown">
                <button class="btn btn-gradient-04 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Действие    </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a href="?tab=add-user" class="dropdown-item" data-toggle="" data-target="#modal-add-user">Добавить пользователя</a>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#responder-user">Отправить сообщение</a>
                </div>
            </div>
        </div>

        <div class="btn-group">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Сортировать     </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="?route=clients">Без сортировки</a>
                    <a class="dropdown-item" href="?route=clients&amp;sort=1">Активные</a>
                    <a class="dropdown-item" href="?route=clients&amp;sort=2">Заблокированные</a>
                    <a class="dropdown-item" href="?route=clients&amp;sort=3">Администраторы</a>
                    <a class="dropdown-item" href="?route=clients&amp;sort=4">Модераторы</a>
                    <a class="dropdown-item" href="?route=clients&amp;sort=5">Наблюдатели</a>
                </div>
            </div>
        </div>

        <div class="btn-group">
            <div class="dropdown">

                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Поиск     </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <form method="get" style="padding: 0 10px;" action="/admin">
                        <input type="text" class="form-control" style="width: 200px;" value="" name="search">
                        <input type="hidden" name="route" value="clients">
                    </form>
                </div>
            </div>
        </div>


    </div>


    <div class="col-lg-12">
        <div class="widget has-shadow">

            <div class="widget-body">
                <div class="table-responsive">

                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th>Логин</th>
                            <th>ФИ</th>
                            <th>Ранг</th>
                            <th>Статус</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = db_query_while("SELECT * FROM users ORDER BY id DESC");
                        foreach ($sql as $user) {
                            ?>
                            <tr id="item10">
                                <td>
                                    <?php
                                    echo $user['login'];
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $user['surname']." ".$user['name'];
                                    ?>
                                </td>
                                <td>
                                    <div class="dropdown">


                                        <button class="btn btn-success dropdown-toggle btn-sm" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <?php echo $settings['name_type_person'][$user['type_person']];?>
                                        </button>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                             x-placement="bottom-start"
                                             style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 25px, 0px);">
                                            <?php
                                            foreach ($settings['name_type_person'] as $key => $name_type_person) {
                                                if($user['type_person'] != $key){
                                                    ?>
                                                    <a class="dropdown-item change-type-client" data-id="<?php echo $user['id'];?>" data-type="<?php echo $key;?>"
                                                       href="#"><?php echo $settings['name_type_person'][$key]; ?></a>
                                            <?php
                                                }
                                            }
                                            ?>

                                        </div>


                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">


                                        <button class="btn dropdown-toggle btn-sm <?php if(!$user['status']){echo "btn-danger";}else{echo "btn-success";}?>" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <?php if($user['status']){
                                                echo "Активен";
                                            }else{
                                                echo "Заблокированн";
                                            }?>
                                        </button>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                             x-placement="bottom-start"
                                             style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 25px, 0px);">

                                            <?php if(!$user['status']){
                                                ?>
                                                <a class="dropdown-item change-status-client" data-id="<?php echo $user['id'];?>" data-status="1"
                                                   href="#">Активировать</a>
                                            <?php
                                            }else{
                                                ?>
                                                <a class="dropdown-item change-status-client" data-id="<?php echo $user['id'];?>" data-status="2"
                                                   href="#">Заблокировать</a>
                                            <?php
                                            }?>

                                        </div>


                                    </div>
                                </td>
                                <td class="td-actions">
                                    <a href="?route=chat&amp;id_user=10" ><i class="la la-paper-plane edit" title="Отправить сообщение"></i></a>
                                    <a href="?route=client_view&amp;id=10" ><i class="la la-edit edit" title="Редактировать"></i></a>
                                    <a href="#" class="delete-user" ><i class="la la-close delete" data-id="<?php echo $user['id'];?>" title="Перевести в черный список"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>



                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>