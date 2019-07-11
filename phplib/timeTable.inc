<?php
// Вывод таблички с помеченными ячейками, которые занимает курс / проект
function showTimeTable($timeSlots) {
    $result = '
        <div class="course-icon">
            <table>
                <tr>
                    <td' . ((in_array(11, $timeSlots)) ? ' class="course-icon-1"' : '')  .'></td>
                    <td' . ((in_array(21, $timeSlots)) ? ' class="course-icon-1"' : '')  .'></td>
                    <td' . ((in_array(31, $timeSlots)) ? ' class="course-icon-1"' : '')  .'></td>
                </tr>
                <tr>
                    <td' . ((in_array(12, $timeSlots)) ? ' class="course-icon-1"' : '')  .'></td>
                    <td' . ((in_array(22, $timeSlots)) ? ' class="course-icon-1"' : '')  .'></td>
                    <td' . ((in_array(32, $timeSlots)) ? ' class="course-icon-1"' : '')  .'></td>
                </tr>
                <tr>
                    <td colspan="3"' . ((in_array(0, $timeSlots)) ? ' class="course-icon-project"' : '')  .'></td>
                </tr>
            </table>
        </div>
        ';
    return $result;
}
?>