<?php
/**
 * MyBB 1.6 Pack de Linguagem Português do Brasil
 * Copyright 2010 MyBB Group, All Rights Reserved
 * http://bf4brasil.com.br
 * $Id: datahandler_event.lang.php 18/01/2014 10:40:30 dthiago $
 */

$l['eventdata_missing_name'] = 'Esta faltando o nome do evento, por favor insira-o.';
$l['eventdata_missing_description'] = 'Esta faltando a descrição do evento, por favor insira-a.';

$l['eventdata_invalid_start_date'] = 'A data inicial do evento é inválida. Tem que inserir o dia, mês tal como o ano e ter a certeza que o dia inserido existe no mês escolhido.';
$l['eventdata_invalid_start_year'] = "Só podem ser criados eventos nos próximos 5 anos. Por favor, escolha um ano inicial para que o evento que seja válido.";
$l['eventdata_invalid_start_month'] = 'O mês inicial escolhido não é válido, escolha um mês válido, por favor.';

$l['eventdata_invalid_end_date'] = 'A data final do evento é inválida. Tem que inserir o dia, mês tal como o ano e ter a certeza que o dia inserido existe no mês escolhido.';
$l['eventdata_invalid_end_year'] = "Só podem ser criados eventos nos próximos 5 anos. Por favor, escolha um ano final válido para o evento.";
$l['eventdata_invalid_end_month'] = 'O mês final escolhido não é válido, escolha um mês final válido.';
$l['eventdata_invalid_end_day'] = 'O dia que inseriu não é um dia válido. Verifique se o dia escolhido não é maior do que o número de dias que o mês tem.';

$l['eventdata_cant_specify_one_time'] = "Se especificou uma data inicial para o evento, tem que especificar uma data final também.";
$l['eventdata_start_time_invalid'] = "A data inicial que inseriu não é valida. Exemplos válidos são 12am, 12:01am, 00:01.";
$l['eventdata_end_time_invalid'] = "A data final que inseriu não é valida. Exemplos válidos são 12am, 12:01am, 00:01.";
$l['eventdata_invalid_timezone'] = "O fuso horário que seleccionou é inválido.";
$l['eventdata_end_in_past'] = "A data final para o evento é mais recente que a inicial.";

$l['eventdata_only_ranged_events_repeat'] = "Apenas eventos com data inicial e final é que podem ser repetidos.";
$l['eventdata_invalid_repeat_day_interval'] = "Inseriu um intervalo de dias inválido.";
$l['eventdata_invalid_repeat_week_interval'] = "Inseriu um intervalo de semanas inválido.";
$l['eventdata_invalid_repeat_weekly_days'] = "Não selecionou nenhum dia da semana para o evento ocorrer.";
$l['eventdata_invalid_repeat_month_interval'] = "Inseriu um intervalo de meses inválido.";
$l['eventdata_invalid_repeat_year_interval'] = "Inseriu um intervalo de anos inválido.";
$l['eventdata_event_wont_occur'] = "Utilizando as datas inicial e final escolhidas em conjunto com as opções de repetição fará com que o evento não ocorra.";

$l['eventdata_no_permission_private_event'] = "Não tem permissões para criar eventos privados.";
?>
