;function NeworderApp()
{
    var app = this;
    
    app.debug = 1;
    
    app.$input = {};
    
    var _init = function(){
        
        app.$input.user = $('.js-user-input');
        
        app.$input.user_id = $('.js-user-id-input');

        app.$input.phone = $('.js-phone-input');
        app.$input.email = $('.js-email-input');
        
        app.$input.lastname = $('.js-lastname-input');
        app.$input.firstname = $('.js-firstname-input');
        app.$input.patronymic = $('.js-patronymic-input');
        app.$input.gender = $('.js-gender-input');
        app.$input.birth = $('.js-birth-input');
        app.$input.birth_place = $('.js-birth-place-input');

        app.$input.passport_serial = $('.js-passport-serial-input');
        app.$input.passport_date = $('.js-passport-date-input');
        app.$input.subdivision_code = $('.js-subdivision-code-input');
        app.$input.passport_issued = $('.js-passport-issued-input');
        
        app.$input.regregion = $('.js-regregion-input');
        app.$input.regregion_type = $('.js-regregion-type-input');
        app.$input.regdistrict = $('.js-regdistrict-input');
        app.$input.regdistrict_type = $('.js-regdistrict-type-input');
        app.$input.regcity = $('.js-regcity-input');
        app.$input.regcity_type = $('.js-regcity-type-input');
        app.$input.reglocality = $('.js-reglocality-input');
        app.$input.reglocality_type = $('.js-reglocality-type-input');
        app.$input.regstreet = $('.js-regstreet-input');
        app.$input.regstreet_type = $('.js-regstreet-type-input');
        app.$input.regindex = $('.js-regindex-input');
        app.$input.reghousing = $('.js-reghousing-input');
        app.$input.regbuilding = $('.js-regbuilding-input');
        app.$input.regroom = $('.js-regroom-input');

        app.$input.faktregion = $('.js-faktregion-input');
        app.$input.faktregion_type = $('.js-faktregion-type-input');
        app.$input.faktdistrict = $('.js-faktdistrict-input');
        app.$input.faktdistrict_type = $('.js-faktdistrict-type-input');
        app.$input.faktcity = $('.js-faktcity-input');
        app.$input.faktcity_type = $('.js-faktcity-type-input');
        app.$input.faktlocality = $('.js-faktlocality-input');
        app.$input.faktlocality_type = $('.js-faktlocality-type-input');
        app.$input.faktstreet = $('.js-faktstreet-input');
        app.$input.faktstreet_type = $('.js-faktstreet-type-input');
        app.$input.faktindex = $('.js-faktindex-input');
        app.$input.fakthousing = $('.js-fakthousing-input');
        app.$input.faktbuilding = $('.js-faktbuilding-input');
        app.$input.faktroom = $('.js-faktroom-input');

        app.$input.workplace = $('.js-workplace-input');
        app.$input.profession = $('.js-profession-input');
        app.$input.workaddress = $('.js-workaddress-input');
        app.$input.workphone = $('.js-workphone-input');
        app.$input.income = $('.js-income-input');
        app.$input.expenses = $('.js-expenses-input');
        app.$input.chief_name = $('.js-chief-name-input');
        app.$input.chief_position = $('.js-chief-position-input');
        app.$input.chief_phone = $('.js-chief-phone-input');
        app.$input.workcomment = $('.js-workcomment-input');

        app.$input.contactperson_name = $('.js-contactperson-name-input');
        app.$input.contactperson_relation = $('.js-contactperson-relation-input');
        app.$input.contactperson_phone = $('.js-contactperson-phone-input');
        app.$input.contactperson2_name = $('.js-contactperson2-name-input');
        app.$input.contactperson2_relation = $('.js-contactperson2-relation-input');
        app.$input.contactperson2_phone = $('.js-contactperson2-phone-input');

//        app.$input. = $('.js--input');
//        app.$input. = $('.js--input');
        
    };
    
    var _init_user_autocomplete = function(){
        app.$input.user.autocomplete({
            serviceUrl:'ajax/search_users.php',
      		minChars:1,
            noCache: true,
            onSelect: function(suggestion){
                
                set_user(suggestion.data);
                
                if (app.debug)
                    console.info('users', suggestion);
    		},
            formatResult: function(suggestion, currentValue){
    		    return suggestion.value;
    		}          
        });        
    };
    
    var set_user = function(data){
        
        app.$input.user_id.val(data.id);

        app.$input.phone.val(data.phone_mobile);
        app.$input.email.val(data.email);

        app.$input.lastname.val(data.lastname);
        app.$input.firstname.val(data.firstname);
        app.$input.patronymic.val(data.patronymic);
        app.$input.gender.find('[value='+data.gender+']').attr('selected', true);
        app.$input.birth.val(data.birth);
        app.$input.birth_place.val(data.birth_place);
        
        app.$input.passport_serial.val(data.passport_serial);
        app.$input.passport_date.val(data.passport_date);
        app.$input.subdivision_code.val(data.subdivision_code);
        app.$input.passport_issued.val(data.passport_issued);

        app.$input.regregion.val(data.Regregion);
        app.$input.regregion_type.val(data.Regregion_shorttype);
        app.$input.regcity.val(data.Regcity);
        app.$input.regcity_type.val(data.Regcity_shorttype);
        app.$input.regdistrict.val(data.Regdistrict);
        app.$input.regdistrict_type.val(data.Regdistrict_shorttype);
        app.$input.reglocality.val(data.Reglocality);
        app.$input.reglocality_type.val(data.Reglocality_shorttype);
        app.$input.regstreet.val(data.Regstreet);
        app.$input.regstreet_type.val(data.Regstreet_shorttype);
        app.$input.reghousing.val(data.Reghousing);
        app.$input.regbuilding.val(data.Regbuilding);
        app.$input.regroom.val(data.Regroom);

        app.$input.faktregion.val(data.Faktregion);
        app.$input.faktregion_type.val(data.Faktregion_shorttype);
        app.$input.faktcity.val(data.Faktcity);
        app.$input.faktcity_type.val(data.Faktcity_shorttype);
        app.$input.faktdistrict.val(data.Faktdistrict);
        app.$input.faktdistrict_type.val(data.Faktdistrict_shorttype);
        app.$input.faktlocality.val(data.Faktlocality);
        app.$input.faktlocality_type.val(data.Faktlocality_shorttype);
        app.$input.faktstreet.val(data.Faktstreet);
        app.$input.faktstreet_type.val(data.Faktstreet_shorttype);
        app.$input.fakthousing.val(data.Fakthousing);
        app.$input.faktbuilding.val(data.Faktbuilding);
        app.$input.faktroom.val(data.faktroom);

        app.$input.workplace.val(data.workplace);
        app.$input.profession.val(data.profession);
        app.$input.workaddress.val(data.workaddress);
        app.$input.workphone.val(data.workphone);
        app.$input.income.val(data.income);
        app.$input.expenses.val(data.expenses);
        app.$input.chief_name.val(data.chief_name);
        app.$input.chief_position.val(data.chief_position);
        app.$input.chief_phone.val(data.chief_phone);
        app.$input.workcomment.val(data.workcomment);

        app.$input.contactperson_name.val(data.contact_person_name);
        app.$input.contactperson_relation.val(data.contact_person_relation);
        app.$input.contactperson_phone.val(data.contact_person_phone);
        app.$input.contactperson2_name.val(data.contact_person2_name);
        app.$input.contactperson2_relation.val(data.contact_person2_relation);
        app.$input.contactperson2_phone.val(data.contact_person2_phone);

//        app.$input..val(data.);
//        app.$input..val(data.);
//        app.$input..val(data.);
//


    };
    
    _init_masks = function(){        
        $('.js-mask-input').each(function(){
            var _mask = $(this).data('mask');
            $(this).inputmask(_mask)
        })
    }
    
    ;(function(){
        _init();
        _init_user_autocomplete();
        _init_masks();
    })();
};

$(function(){
    new NeworderApp();
});