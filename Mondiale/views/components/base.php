<script>
    window.onload = () => {
        document.addEventListener('contextmenu', (e) => e.preventDefault());
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey && e.shiftKey && e.key == 'I') || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.key == 'J') || // Ctrl+Shift+J
                (e.key === 'F12')) { // F12
                e.preventDefault();
            }
        });
    }
</script>
<script type="text/javascript">
    function checkValue(str, max) {
        if (str.charAt(0) !== '0' || str == '00') {
            var num = parseInt(str);
            if (isNaN(num) || num <= 0 || num > max) num = 1;
            str = num > parseInt(max.toString().charAt(0)) && num.toString().length == 1 ? '0' + num : num.toString();
        };
        return str;
    };

    function date_reformat_dd(date) {
        date.addEventListener('input', function(e) {
            this.type = 'text';
            var input = this.value;
            if (/\D\/$/.test(input)) input = input.substr(0, input.length - 3);
            var values = input.split('/').map(function(v) {
                return v.replace(/\D/g, '')
            });
            if (values[1]) values[1] = checkValue(values[1], 12);
            if (values[0]) values[0] = checkValue(values[0], 31);
            var output = values.map(function(v, i) {
                return v.length == 2 && i < 2 ? v + '/' : v;
            });
            this.value = output.join('').substr(0, 14);
        });
    }
</script>
<script>
    function validlun(input) {
        let cardNumber = input.value.replaceAll(" ", "");
        // if (!/^\d{16}$/.test(cardNumber)) {
        //     input.setCustomValidity('Număr de card invalid');
        //     return;
        // }
        let sum = 0;
        let digit;
        let addend;
        let doubled;
        for (let i = 0; i < cardNumber.length; i++) {
            digit = parseInt(cardNumber.charAt(i));
            if (i % 2 === 0) {
                addend = digit * 2;
                if (addend > 9) {
                    addend -= 9;
                }
                sum += addend;
            } else {
                sum += digit;
            }
        }
        if (sum % 10 === 0) {
            input.setCustomValidity('');
            console.log(2);
        } else {
            console.log(1);
            input.setCustomValidity('Invalid card number');
        }
    }

    function formatString(e) {
        var inputChar = String.fromCharCode(event.keyCode);
        var code = event.keyCode;
        var allowedKeys = [8];
        if (allowedKeys.indexOf(code) !== -1) {
            return;
        }

        event.target.value = event.target.value.replace(
            /^([1-9]\/|[2-9])$/g, '0$1/' // 3 > 03/
        ).replace(
            /^(0[1-9]|1[0-2])$/g, '$1/' // 11 > 11/
        ).replace(
            /^([0-1])([3-9])$/g, '0$1/$2' // 13 > 01/3
        ).replace(
            /^(0?[1-9]|1[0-2])([0-9]{2})$/g, '$1/$2' // 141 > 01/41
        ).replace(
            /^([0]+)\/|[0]+$/g, '0' // 0/ > 0 et 00 > 0
        ).replace(
            /[^\d\/]|^[\/]*$/g, '' // Chiffre & / only `/`
        ).replace(
            /\/\//g, '/' // Evit d'avoir plus de 1 `/`
        );
    }
</script>