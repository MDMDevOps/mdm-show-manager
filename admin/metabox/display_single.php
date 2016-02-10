<tr class="timeslot" data-index="<?php echo $index; ?>" data-rid="<?php echo $airtime['nonce']; ?>">
    <td class="day">
        <span class="display"><?php echo $this->get_day_name( $airtime['show']['sday'] ); ?></span>
        <span class="input"><input class="hidden" type="text" name="onair[<?php echo $index; ?>][show][sday]" value="<?php echo $airtime['show']['sday']; ?>" readonly></span>
    </td>
    <td class="stime">
        <span class="display"><?php echo $airtime['show']['stime']->format( 'g:i A' ); ?></span>
        <span class="input"><input class="hidden" type="text" name="onair[<?php echo $index; ?>][show][stime]" value="<?php echo $airtime['show']['stime']->format( 'H:i:s' ); ?>" readonly></span>
    </td>
    <td class="etime">
        <span class="display"><?php echo $airtime['show']['etime']->modify( '+ 1 second' )->format( 'g:i A' ); ?></span>
        <span class="input"><input class="hidden" type="text" name="onair[<?php echo $index; ?>][show][etime]" value="<?php echo $airtime['show']['etime']->format( 'H:i:s' ); ?>" readonly></span>
    </td>
    <td class="duration">
        <span class="display"><?php echo sprintf( '%s hrs', $airtime['show']['duration'] ); ?></span>
        <span class="input"><input class="hidden" type="text" name="onair[<?php echo $index; ?>][show][duration]" value="<?php echo$airtime['show']['duration']; ?>" readonly></span>
    </td>
    <td class="action"><button data-action="remove" class="button remove-record">Delete</button></td>
</tr>