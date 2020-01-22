<div class="toolkit">
    <p>
        Добавить пользователя
    </p>
    <script>$("title").text("Добавить пользователя"); </script>
</div>
<div class="main_page col-lg-12">
    <div class="profile-page col-lg-9" style="margin: auto">


        <form class="form-data-profile-add">

            <div class="alert alert-danger alert-none" role="alert"></div>
            <div class="alert alert-success alert-none" role="alert"></div>

<!--Фак фак фак-->
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Логин</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control" autocomplete="off" name="login" value="<?php echo $_SESSION['profile']['login']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Имя</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control" autocomplete="off" name="name" value="<?php echo $_SESSION['profile']['name']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Фамилия</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control" autocomplete="off" name="surname" value="<?php echo $_SESSION['profile']['surname']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Почта</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control" autocomplete="off" name="email" value="<?php echo $_SESSION['profile']['email']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Телефон</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control" autocomplete="off" name="phone" value="<?php echo $_SESSION['profile']['phone']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Пароль</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="password" class="form-control" autocomplete="off" name="password" value="<?php echo ""; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Тип аккаунта</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="dropdown bootstrap-select change-filter-item"><select name="type_person"
                                                                                      title="Тип аккаунта"
                                                                                      class="change-filter-item selectpicker"
                                                                                      tabindex="0">


                            <?php echo $CategoryAll->outNameTypePerson($_SESSION['profile']['type_person']); ?>

                        </select>
                        <div class="dropdown-menu " role="combobox">
                            <div class="inner show" role="listbox" aria-expanded="false" tabindex="-1">
                                <ul class="dropdown-menu inner show"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Статус аккаунта</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="dropdown bootstrap-select change-filter-item"><select name="status"
                                                                                      title="Статус аккаунта"
                                                                                      class="change-filter-item selectpicker"
                                                                                      tabindex="0">



                            <option value="1" selected="">Активен</option>';
                            <option value="0">Заблокирован</option>';

                        </select>
                        <div class="dropdown-menu " role="combobox">
                            <div class="inner show" role="listbox" aria-expanded="false" tabindex="-1">
                                <ul class="dropdown-menu inner show"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6"><input type="submit" class="btn btn-success btn-block" value="Сохранить"><br></div>
                <div class="col-lg-3"></div>
            </div>

        </form>
    </div>
</div>