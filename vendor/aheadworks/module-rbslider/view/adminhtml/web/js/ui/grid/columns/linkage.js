define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Rbslider/ui/grid/cells/linkage'
        },
        getLinkageData: function(row) {
            return row[this.index];
        }
    });
});
