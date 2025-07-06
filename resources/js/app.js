import './bootstrap';
import './echo';

// let intervalId = null;
// const REFRESH_INTERVAL_MS = 60000;

// function startHeartbeatLoop(currentUserId, channelName) {
//   if (intervalId || document.visibilityState !== 'visible') return; // Đã chạy thì không chạy lại

//   intervalId = setInterval(() => {
//     const socketId = Echo.socketId();
//     if (!socketId || !currentUserId || !channelName) return;

//     // 1️⃣ Refresh dữ liệu Redis của channel
//     axios.post('/api/refresh-channel', {
//       channel: channelName
//     }).catch((err) => {
//       console.warn('Failed to refresh channel TTL:', err);
//     });

//     // 2️⃣ Gửi heartbeat user để backend biết socket còn sống
//     axios.post('/api/socket/heartbeat', {
//       user_id: currentUserId,
//       socket_id: socketId
//     }).catch((err) => {
//       console.warn('Failed to send user heartbeat:', err);
//     });

//   }, REFRESH_INTERVAL_MS);
// }

// function stopHeartbeatLoop() {
//   if (intervalId) {
//     clearInterval(intervalId);
//     intervalId = null;
//     console.log('Stopped heartbeat loop');
//   }
// }

// // Theo dõi trạng thái tab
// document.addEventListener('visibilitychange', () => {
//   if (document.visibilityState === 'visible') {
//     startHeartbeatLoop(currentUserId, 'chat-room-1');
//   } else {
//     stopHeartbeatLoop();
//   }
// });

// const handleVisibility = debounce(() => {
//   if (document.visibilityState === 'visible') {
//     startHeartbeatLoop(currentUserId, 'chat-room-1');
//   } else {
//     stopHeartbeatLoop();
//   }
// }, 3000);

// const handleVisibility = throttle(() => {
//   if (document.visibilityState === 'visible') {
//     startHeartbeatLoop(currentUserId, 'chat-room-1');
//   } else {
//     stopHeartbeatLoop();
//   }
// }, 2000);

//document.addEventListener('visibilitychange', handleVisibility);

// // Dừng ping khi tab đóng
// window.addEventListener('beforeunload', () => {
//   stopHeartbeatLoop();
// });

// // Gọi ở đâu đó khi đã có thông tin user & channel
// // startHeartbeatLoop(currentUserId, 'chat-room-1');

// // Chờ 1 giây sau khi ngưng chuyển tab mới xử lý
// const debounce = (fn, delay) => {
//   let timer;
//   return (...args) => {
//     clearTimeout(timer);
//     timer = setTimeout(() => fn(...args), delay);
//   };
// };

// Không cho xử lý lại trong vòng 2 giây
// const throttle = (fn, limit) => {
//   let inThrottle;
//   return (...args) => {
//     if (!inThrottle) {
//       fn(...args);
//       inThrottle = true;
//       setTimeout(() => inThrottle = false, limit);
//     }
//   };
// };