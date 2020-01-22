
<div class="toolkit">
    <p>
        Настройки
    </p>
    <script>$("title").text("Настройки"); </script>
</div>
<div class="main_page col-lg-12">
    <div class="profile-page col-lg-9" style="margin: auto">


        <form class="form-data-settings-save">

            <div class="alert alert-danger alert-none" role="alert"></div>
            <div class="alert alert-success alert-none" role="alert"></div>


            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Кол-во выводимых записей на страницу</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="number" class="form-control col-lg-2" autocomplete="off" name="page_output" value="<?php echo $settings['settings']['page_output']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Часовой пояс</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="number" class="form-control col-lg-2" autocomplete="off" name="time_zone" value="<?php echo $settings['settings']['time_zone']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Цвет полей таблицы</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control col-lg-2" style="border-color: <?php echo $settings['settings']['color_table_line']; ?>;" autocomplete="off" name="color_table_line" value="<?php echo $settings['settings']['color_table_line']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Цвет четных полей таблицы</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="box-searchCity">
                        <input type="text" class="form-control col-lg-2" style="border-color: <?php echo $settings['settings']['color_table_line_2']; ?>;" autocomplete="off" name="color_table_line_2" value="<?php echo $settings['settings']['color_table_line_2']; ?>">
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Рассылка</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-light btn-sm <?php if($settings['settings']['mailing']){echo "active";}?>">
                            <input type="radio" name="mailing" value="1" <?php if($settings['settings']['mailing']){echo "checked=''";}?> > Активна
                        </label>
                        <label class="btn btn-light btn-sm <?php if(!$settings['settings']['mailing']){echo "active";}?>">
                            <input type="radio" name="mailing" value="0" <?php if(!$settings['settings']['mailing']){echo "checked=''";}?>> Отключена
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Текст рассылки</label>
                <div class="col-lg-8 col-xl-6">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <textarea name="text_message" id="text_message" cols="100" rows="3" style="padding: 10px;"><?php echo $settings['settings']['text_message']; ?></textarea>
                    </div>
                    <small id="leng_lest">Осталось символов</small>
                    <small>Домен {----name_domain----} | Телефон {--phone--}</small>
                </div>
            </div>


















            <br>

            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6"><input type="submit" class="btn btn-success btn-block profile-edit-data" value="Сохранить"><br></div>
                <div class="col-lg-3"></div>
            </div>

        </form>
    </div>
</div>