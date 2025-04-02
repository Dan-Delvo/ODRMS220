import './bootstrap';

// Initialize Firebase and Firebase Messaging
import { initializeApp } from "firebase/app";
import { getMessaging, getToken } from "firebase/messaging";

// Firebase configuration (replace with your own)
const firebaseConfig = {
    apiKey: "AIzaSyCv603uest8suBfBtw-PjRRf_ID8myUnS0",
    authDomain: "odrms-pwa.firebaseapp.com",
    projectId: "odrms-pwa",
    storageBucket: "odrms-pwa.firebasestorage.app",
    messagingSenderId: "340988917251",
    appId: "1:340988917251:web:e34e35953b56c2e3d44036"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Request permission to show notifications
Notification.requestPermission().then(function(permission) {
    if (permission === 'granted') {
        console.log('Notification permission granted.');
        // Get the FCM token
        getToken(messaging, { vapidKey: 'your-vapid-key' }).then((currentToken) => {
            if (currentToken) {
                console.log('FCM Token: ', currentToken);
                // Store or send this token to your server later
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('Error getting FCM token: ', err);
        });
    } else {
        console.log('Notification permission denied.');
    }
});
