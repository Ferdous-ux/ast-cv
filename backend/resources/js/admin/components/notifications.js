/*
=========================================================
AST-CV ADMIN
Notifications Component
=========================================================
*/


document.addEventListener(
    'DOMContentLoaded',
    function () {



        const button =
            document.querySelector(
                '.admin-notification'
            );


        const dropdown =
            document.querySelector(
                '.admin-notification-dropdown'
            );



        if (!button || !dropdown) {
            return;
        }





        button.addEventListener(
            'click',
            function (event) {


                event.stopPropagation();


                dropdown.classList.toggle(
                    'is-open'
                );


            }
        );







        document.addEventListener(
            'click',
            function () {


                dropdown.classList.remove(
                    'is-open'
                );


            }
        );







        dropdown.addEventListener(
            'click',
            function(event){

                event.stopPropagation();

            }
        );



    }
);