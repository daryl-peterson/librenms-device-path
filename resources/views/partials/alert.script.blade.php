<script>
    $(document).ready(function() {
        console.log("LibreNMS custom script running!");
        $(".alert").delay(5000).fadeOut(500, function() {
            $(this).alert('close');
        });
    });
</script>
