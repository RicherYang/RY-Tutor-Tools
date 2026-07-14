import $ from 'jquery';

import './main.scss';

$(function () {
    $('.tutor-form-toggle-input').on('change', function () {
        const name = $(this).prev().attr('name');
        const status = $(this).prev().val() === 'on';
        switch (name) {
            case 'tutor_option[RY_ecpay_testmode]':
                if (status) {
                    $('#field_RY_ecpay_testmode').addClass(['tutor-option-no-bottom-border']);
                } else {
                    $('#field_RY_ecpay_testmode').removeClass(['tutor-option-no-bottom-border']);
                }
                ['MerchantID', 'HashKey', 'HashIV'].forEach((field) => {
                    if (status) {
                        $(`#field_RY_ecpay_${field}`).addClass(['tutor-hide-option']);
                    } else {
                        $(`#field_RY_ecpay_${field}`).removeClass(['tutor-hide-option']);
                    }
                });
                break;
            case 'tutor_option[RY_smilepay_testmode]':
                if (status) {
                    $('#field_RY_smilepay_testmode').addClass(['tutor-option-no-bottom-border']);
                } else {
                    $('#field_RY_smilepay_testmode').removeClass(['tutor-option-no-bottom-border']);
                }
                ['Dcvc', 'Rvg2c', 'Verifykey', 'Rotcheck'].forEach((field) => {
                    if (status) {
                        $(`#field_RY_smilepay_${field}`).addClass(['tutor-hide-option']);
                    } else {
                        $(`#field_RY_smilepay_${field}`).removeClass(['tutor-hide-option']);
                    }
                });
                break;
        }
    }).trigger('change');
});
