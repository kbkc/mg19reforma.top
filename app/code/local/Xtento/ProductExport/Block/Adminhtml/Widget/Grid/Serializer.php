<?php

if (!@class_exists('Xtento_ProductExport_Block_Adminhtml_Widget_Grid_Serializer')) {
    class Xtento_ProductExport_Block_Adminhtml_Widget_Grid_Serializer extends Mage_Adminhtml_Block_Widget_Grid_Serializer
    {
        protected function _afterToHtml($html)
        {
            $js = <<<EOT
<script type="text/javascript">
    serializerController.prototype.rowClick = function (grid, event) {
        if (typeof Event.findElement(event, 'a') == 'undefined') { // Dont call the checkbox method if the link or action column is clicked
            var trElement = Event.findElement(event, 'tr');
            var isInput = Event.element(event).tagName == 'INPUT';
            if (trElement) {
                var checkbox = Element.select(trElement, 'input');
                if (checkbox[0] && !checkbox[0].disabled) {
                    var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    this.grid.setCheckboxChecked(checkbox[0], checked);
                }
            }
            this.getOldCallback('row_click')(grid, event);
        }
    };
</script>
EOT;
            return $js . parent::_afterToHtml($html);
        }
    }
}