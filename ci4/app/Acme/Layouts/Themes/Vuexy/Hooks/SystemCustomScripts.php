<!-- Insert these scripts at the bottom of the HTML, but before you use any Firebase services -->

<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.6.2/firebase-app.js"></script>

<!-- If you enabled Analytics in your project, add the Firebase SDK for Analytics -->
<script src="https://www.gstatic.com/firebasejs/7.6.2/firebase-analytics.js"></script>

<!-- Add Firebase products that you want to use -->
<script src="https://www.gstatic.com/firebasejs/7.6.2/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.6.2/firebase-firestore.js"></script>

<script>

    //acme_render_script_imports($scriptData);

    //TODO - only render if the user has setup firebase and the config data is valid

    <?php if(acme_get_env("acme.config.system.firebase.enabled", "bool")){ ?>

    var firebaseConfig = {

        apiKey: "<?php echo $config["system"]["firebase.api.key"]; ?>",
        authDomain: "<?php echo $config["system"]["firebase.auth.domain"]; ?>",
        databaseURL: "<?php echo $config["system"]["firebase.database.url"]; ?>",
        projectId: "<?php echo $config["system"]["firebase.project.id"]; ?>",
        storageBucket: "<?php echo $config["system"]["firebase.storage.bucket"]; ?>",
        messagingSenderId: "<?php echo $config["system"]["firebase.messaging.sender.id"]; ?>",
        appId: "<?php echo $config["system"]["firebase.app.id"]; ?>",
        measurementId: "<?php echo $config["system"]["firebase.measurement.id"]; ?>"

    };

    // TODO descrypt data for secuity reasoon, enc key ???
    
    //firebase.initializeApp(firebaseConfig);

    <?php } ?>

</script>
