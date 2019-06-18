<?php
/**
 * WDD4 - Fall/2018
 * PHP Capstone Project
 * Instructor: Steve George
 * Include file: Footer tag
 * Student: Alessandra Diniz
 * Date: May/11/2019
 */
?>

    <footer>
        <div id="inner_footer">
            <div id="address">
                <p>Bella Dance School</p>
                <p>460 Portage Ave -  R99 99R</p>
                <p>Winnipeg - MB</p>
                <p>(204) 333-4444</p>
                <p>contact@bella-dance.ca</p>
            </div>

            <div id="social_media_icons">
                <p>
                    <a id="facebook" href="#"></a>
                    <a id="instagram" href="#"></a>
                    <a id="twitter" href="#"></a>
                    <a id="youtube" href="#"></a>
                </p>
            </div>
            <br/>
            <p id="copyright">Copyright &copy; 2019 - Bella Dance School</p>

        </div>
    </footer>

    <script>
        function footer(){

            //Get height of main
            var main = document.getElementsByTagName("main")[0];
            var style = getComputedStyle(main);
            main_h = parseFloat(style.height);

            //Get height of header
            var header = document.getElementsByTagName("header")[0];
            var style = getComputedStyle(header);
            header_h = parseFloat(style.height);

            //Get height of footer
            var footer = document.getElementsByTagName("footer")[0];
            var style = getComputedStyle(footer);
            footer_h = parseFloat(style.height);

            //Calculate bottom position for footer
            bottom = (- main_h + header_h + footer_h) ;

            document.getElementsByTagName("footer")[0].style.bottom = bottom + 'px';
        }

        footer();

    </script>
</body>

</html>
