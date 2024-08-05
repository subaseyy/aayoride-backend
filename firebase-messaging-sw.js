importScripts("https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js");

firebase.initializeApp({
    apiKey: "AIzaSyBkFEj_PILbuy6JYDQLyBPiUHwg0Wej42c",
    authDomain: "hexaride-fbdac.firebaseapp.com",
    projectId: "hexaride-fbdac",
    storageBucket: "hexaride-fbdac.appspot.com",
    messagingSenderId: "30301959388",
    appId: "1:30301959388:web:dc33ee586670882be57019",
    measurementId: "G-ZTLK7ZL3TH"
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
    const promiseChain = clients
        .matchAll({
            type: "window",
            includeUncontrolled: true
        })
        .then(windowClients => {
            for (let i = 0; i < windowClients.length; i++) {
                const windowClient = windowClients[i];
                windowClient.postMessage(payload);
            }
        })
        .then(() => {
            const title = payload.notification.title;
            const options = {
                body: payload.notification.score
              };
            return registration.showNotification(title, options);
        });
    return promiseChain;
});
self.addEventListener('notificationclick', function (event) {
    console.log('notification received: ', event)
});