//scroll function
window.addEventListener("scroll", function() {
    if (window.scrollY > 900) {
        document.getElementById("welcome_top").style.display = "none";
    }
});
//time.sleep function
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
//add card function
function add_card_show() {
    const add_card_button = document.getElementById("add_card");
    const myElement = document.getElementById("add_card_function");

    add_card_button.addEventListener("click", function(event) {
        event.preventDefault();
        myElement.style.display = myElement.style.display = "flex";
    });
}
//add money function
function add_money_show() {
    const add_card_button = document.getElementById("account_button_add_money");
    const myElement_input = document.getElementById("account_add_money");
    const myElement_button = document.getElementById("account_button_add_money1");
    const myElement_none1 = document.getElementById("account_button_add_money");
    const myElement_none2 = document.getElementById("account_button_send_money");
    const myElement_none3 = document.getElementById("account_send_money");
    const myElement_none4 = document.getElementById("account_button_send_money1");
    const myElement_transaction_name = document.getElementById("account_transaction_name");
    const close_transaction = document.getElementById("close_transaction");

    add_card_button.addEventListener("click", function(event) {
        event.preventDefault();
        myElement_input.style.display = myElement_input.style.display = "inline-block";
        myElement_button.style.display = myElement_button.style.display = "inline-block";
        myElement_transaction_name.style.display = myElement_transaction_name.style.display = "inline-block";
        close_transaction.style.display = close_transaction.style.display = "inline-block";
        myElement_none1.style.display = myElement_none1.style.display = "none";
        myElement_none2.style.display = myElement_none2.style.display = "none";
        myElement_none3.style.display = myElement_none3.style.display = "none";
        myElement_none4.style.display = myElement_none4.style.display = "none";
    });
}
//send money function
function send_money_show() {
    const add_card_button = document.getElementById("account_button_send_money");
    const myElement_input = document.getElementById("account_send_money");
    const myElement_button = document.getElementById("account_button_send_money1");
    const myElement_none1 = document.getElementById("account_button_add_money");
    const myElement_none2 = document.getElementById("account_button_send_money");
    const myElement_none3 = document.getElementById("account_add_money");
    const myElement_none4 = document.getElementById("account_button_add_money1");
    const myElement_transaction_name = document.getElementById("account_transaction_name");
    const close_transaction = document.getElementById("close_transaction");

    add_card_button.addEventListener("click", function(event) {
        event.preventDefault();
        myElement_input.style.display = myElement_input.style.display = "inline-block";
        myElement_button.style.display = myElement_button.style.display = "inline-block";
        myElement_transaction_name.style.display = myElement_transaction_name.style.display = "inline-block";
        close_transaction.style.display = close_transaction.style.display = "inline-block";
        myElement_none1.style.display = myElement_none1.style.display = "none";
        myElement_none2.style.display = myElement_none2.style.display = "none";
        myElement_none3.style.display = myElement_none3.style.display = "none";
        myElement_none4.style.display = myElement_none4.style.display = "none";
    });
}
//hide transaction option
function transaction_hide(){
    //send money
    const add_card_send_money = document.getElementById("account_button_send_money");
    const send_money_input = document.getElementById("account_send_money");
    const send_money_button = document.getElementById("account_button_send_money1");
    //add money
    const add_card_add_money = document.getElementById("account_button_add_money");
    const add_money_input = document.getElementById("account_add_money");
    const add_money_button = document.getElementById("account_button_add_money1");
    //functionality
    const myElement_transaction_name = document.getElementById("account_transaction_name");
    const close_transaction = document.getElementById("close_transaction");


    add_card_send_money.style.display = add_card_send_money.style.display = "inline-block";
    add_card_add_money.style.display = add_card_add_money.style.display = "inline-block";

    send_money_input.style.display = send_money_input.style.display = "none";
    add_money_input.style.display = add_money_input.style.display = "none";

    send_money_button.style.display = send_money_button.style.display = "none";
    add_money_button.style.display = add_money_button.style.display = "none";

    myElement_transaction_name.style.display = myElement_transaction_name.style.display = "none";
    close_transaction.style.display = close_transaction.style.display = "none";
}
//check blank_on_register
function check_register(){
    const name = document.getElementById("register_name_form");
    const surname = document.getElementById("register_surname_form");
    const nickname = document.getElementById("register_nickname_form");
    const email = document.getElementById("register_email_form");
    const phone_number = document.getElementById("register_phone_form");
    const password1 = document.getElementById("register_password1_form");
    const password2 = document.getElementById("register_password2_form");
    const warning = document.getElementById("register_name_form_warning");

    document.querySelector("form").addEventListener("submit", function(event) {
        if (!name.value || !surname.value || !nickname.value || !email.value || !phone_number.value || !password1.value || !password2.value) {
            event.preventDefault();
            warning.style.display = "block";
            var arr = [name, surname, nickname, email, phone_number, password1, password2];
            arr.forEach(function(check){
                if (!check.value){
                    check.style.borderBottom = "0.4vh solid #cc0000";
                }
                else if (check.value){
                    check.style.borderBottom = "0.4vh solid black";
                }
            })
        } else {
            warning.style.display = "none";
        }
    });
}
//dropdown
var i = 0;
 async function dropdownFunction() {
    const dropdown_card = document.getElementById("myDropdown");
    const dropdown_arrow = document.getElementById("drop_down_arrow1");
    const dropdown_arrow2 = document.getElementById("drop_down_arrow2");

    if (i === 0) {
        dropdown_card.classList.replace("end_animations_dropdown","navbar_own_account_button_dropdown_content");
        dropdown_card.style.display = dropdown_card.style.display = "flex";
        dropdown_arrow.style.display = dropdown_arrow.style.display = "none";
        dropdown_arrow2.style.display = dropdown_arrow2.style.display = "inline-block";
        i++;
    } else {
        dropdown_card.classList.toggle("end_animations_dropdown");
        await sleep(500);
        dropdown_card.style.display = dropdown_card.style.display = "none";
        dropdown_arrow.style.display = dropdown_arrow.style.display = "inline-block";
        dropdown_arrow2.style.display = dropdown_arrow2.style.display = "none";
        i--;
    }
}
//button delete
function confirmDeleteCard() {
    if (confirm("Are you sure you want to delete card?")) {
        return true; // execute the form submit action
    } else {
        return false; // cancel the form submit action
    }
}
//hide add_card
function hideAddCard(){
    const myElement = document.getElementById("add_card_function");
    myElement.style.display = myElement.style.display = "none";
}


//add card function - calls the add card function
document.addEventListener("DOMContentLoaded", add_card_show);
document.addEventListener("DOMContentLoaded", check_register);
document.addEventListener("DOMContentLoaded", add_money_show);
document.addEventListener("DOMContentLoaded", send_money_show);

