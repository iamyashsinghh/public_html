importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
firebase.initializeApp({
    apiKey: "AIzaSyB4GRKlQxStAeS69WuMlDQcdPP6mCSBsaw",
    authDomain: "weddingbanquetsfcm.firebaseapp.com",
    projectId: "weddingbanquetsfcm",
    storageBucket: "weddingbanquetsfcm.appspot.com",
    messagingSenderId: "1058988997700",
    appId: "1:1058988997700:web:5d21d9cc1610a9314a9011",
    measurementId: "G-NXRL2FM5W2"
});
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png",
    };
    return self.registration.showNotification(
        title,
        options,
    );
});
