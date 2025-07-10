<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>عجلة الحظ</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.rtl.min.css" integrity="sha384-q8+l9TmX3RaSz3HKGBmqP2u5MkgeN7HrfOJBLcTgZsQsbrx8WqqxdA5PuwUV9WIx" crossorigin="anonymous">

  <style>
    body {
  direction: rtl;
  font-family: Cairo,Arial, sans-serif;
  margin: 0;
  padding: 0px 5px;
  text-align: center;
  color: white;
  overflow-y: scroll;
  background-image: url('../images/bg.jpg'); /* ضع هنا مسار الصورة */
  background-repeat: no-repeat;
  background-size: cover;   /* تجعل الصورة تغطي كامل الخلفية */
  background-position: center center; /* توضع مركزية */
  background-attachment: fixed; /* تجعل الخلفية ثابتة أثناء التمرير */
  background-color: #ffffff; /* لون احتياطي في حال لم تحمل الصورة */
}


.wheel-container {
  position: relative;
  width: 500px;
  height: 500px; /* تأكدنا من ارتفاع الحاوية */
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}



#wheel {
  background-color: radial-gradient(circle at center, #d81b60, #880e4f); /* خلفية التدرج السابقة */
  background-repeat: no-repeat, no-repeat;
  background-position: center center, center center;
  background-size: contain, cover;
  box-shadow: inset 0 0 50px rgba(255, 255, 255, 0.3),
              0 0 15px #6e3d37;
  border-radius: 50%;
  /* border: 8px solid #f48fb1; */
  width: 100%;
  height: 100%;
  display: block;
}

#spin {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 75px;
  height: 75px;
  border-radius: 50%;

  /* تدرج لوني يشبه الشمس: أغمق خارجيًا وأفتح داخليًا */
  background: radial-gradient(circle at center,
              #fff4bc 0%,   /* أصفر فاتح جدًا في المركز */
              #f9d423 40%,  /* أصفر شمعي متوسط */
              #f08a00 75%,  /* برتقالي ذهبي */
              #c56b00 100%);/* بني غامق على الحواف */

  color: #4a2e00; /* لون نص داكن يناسب الأصفر */
  font-size: 20px;
  font-weight: bold;
  border: none;
  cursor: pointer;
  box-shadow: 0 0 10px 4px rgba(240, 138, 0, 0.8);
  transition: box-shadow 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  user-select: none;
}

#spin:hover {
  box-shadow: 0 0 30px 8px rgba(240, 138, 0, 1);
}







#loadingOverlay {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.6); /* تغطية شفافة داكنة */
  z-index: 9999; /* فوق كل شيء */
  display: flex;
  justify-content: center;
  align-items: center;
}

.spinner {
  border: 8px solid rgba(255, 255, 255, 0.3);
  border-top: 8px solid white;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}

/* حركة دوران الاسبنر */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}



  </style>
   <script src="https://telegram.org/js/telegram-web-app.js"></script>
</head>
<body>

<audio id="spinSound" src="{{ asset('ring.mp3') }}" preload="auto" class="d-none"></audio>
<div class="row align-items-center align-content-between align-content-md-center" style="height: 100vh;width:100vw;margin:0px auto">
  <div class="col-12 col-md-3 order-1 order-md-3"><img src="{{ asset('images/log.png') }}" style="width: 50%;"/></div>
  <div class="col-12 col-md-6 order-2">
    <div class="wheel-container" style="width: 300px; height: 300px; position: relative;">
  <canvas id="wheel" width="300" height="300" style="display: block;"></canvas>
  <img src="{{ asset('images/bord.png') }}" alt="إطار ذهبي"
       style="position: absolute; top: 0;top: -48px;left: -48px;width: 395px; pointer-events: none;">
  <button  id="spin" style="margin-top: 10px;">انتظر</button>
</div>
    </div>
      <div class="col-21 col-md-3  order-3 order-md-1 pb-3 pb-md-0">
        <h1  id="result" class="h1 display-4 fw-bold">عجلة الحظ</h1>
        <h4 class="mt-3" id="message" class="text-warning" style="border-radius: 10px;
    background: rgba(0, 0, 0, 0.5);">جارٍ جلب معرف المستخدم...</h4>
      </div>
   </div>

<div id="loadingOverlay" style="display: none;">
  <div class="spinner"></div>
</div>

<script>

const canvas = document.getElementById('wheel');
const ctx = canvas.getContext('2d');
const spinButton = document.getElementById('spin');
const messageh = document.getElementById('message');
var userRotate = 4;
var userId = null;
var rotId;
const token = '1|JrqSlcvhpxY6Gdv2Wiggyrg7n3Fd8Q16mza8AeArc249fbcf';

window.onload = function() {

var message =@json($message);
console.log(message);
messageh.innerHTML = "<spin class='text-warning'>"+@json($msgtext)+"</spin>";
if(message==="success"){
userRotate = @json($result);
console.log(userRotate);
console.log(typeof userRotate);
rotId = @json($rotId);
console.log("rotId:");
console.log(rotId);
console.log(typeof rotId);
spinButton.disabled = false;
spinButton.innerText = "دوّر";
}else{
    spinButton.innerHTML = "<span style='font-size: 15px;'>غير مسموح</span>";
}

    //   fetch('https://jsonplaceholder.typicode.com/todos/1')
    //   .then(response => response.json())
    //   .then(json => messageh.innerText=json.userId);

//       if (window.Telegram && window.Telegram.WebApp) {

//         Telegram.WebApp.ready();

//         // تأكد أن initDataUnsafe موجودة ولديها user و id
//         if (
//           window.Telegram.WebApp.initDataUnsafe &&
//           window.Telegram.WebApp.initDataUnsafe.user &&
//           window.Telegram.WebApp.initDataUnsafe.user.id
//         ) {

//           userId = window.Telegram.WebApp.initDataUnsafe.user.id;

//           if (userId) {
//                 messageh.innerText = 'معرف المستخدم: ' + userId;
//                   // استبدل هذا بالتوكن الفعلي الذي لديك
//                 fetch('https://kingdomsyr.com/api/receive_wheel_user_id', {
//                   method: 'POST',
//                   headers: {
//                     'Content-Type': 'application/json',
//                     'Accept': 'application/json',
//                     'Authorization': 'Bearer ' + token,
//                     // 'Access-Control-Allow-Origin':'*',
//                     // 'X-Requested-With': 'XMLHttpRequest'
//                   },
//                   body: JSON.stringify({ user_id: userId }),
//                 })
//                 .then(response => {
//                   if (!response.ok) {
//     return response.text().then(function(text) {
//       throw new Error('خطأ في الشبكة: ' + response.status + ' - ' + text);
//     });
//   }
//                   return response.json();
//                 })
//                 .then(data => {
//                   console.log('استجابة API:', data);
//                   // يمكنك تحديث واجهة المستخدم هنا بناءً على استجابة الخادم
//                   if(data.message=="success"){
//                     userRotate = data.result;
//                     rotId = data.rotId;
//                     spinButton.disabled = false;
//                     spinButton.innerText = "دوّر";
//                     console.log('قيمة result:', userRotate);
//                   }else if(data.message=="exists"){
//                     result.innerHTML="<h3 class='text-warning'>لقد استنفذت عدد المرات المسموحة لليوم</h3>";
//                     spinButton.innerText = "غير متاحة";
//                   }else if(data.message=="notcharge"){
//                     result.innerHTML="<h3 class='text-warning'>يجب أولاً أن تقوم بعملية شحن بقيمة 10000 خلال اليوم</h3>";
//                     spinButton.innerText = "غير متاحة";
//                   }else{
//                     result.innerHTML="<h3 class='text-danger'>خطأ في معالجة البيانات</h3>";
//                     spinButton.innerText = "خطأ";
//                   }
//                 })
//                 .catch(error => {
//                   console.error('خطأ أثناء إرسال الطلب:', error);

//                 var errorMessage = 'خطأ: ' + (error.message || error) + 'n';
//                 errorMessage += 'النوع: ' + (error.name || 'غير معروف') + 'n';

//                 if (error.stack) {
//                     errorMessage += 'تفاصيل:n' + error.stack;
//                 }

//                 messageh.innerText = errorMessage;
//                 })
//                 .finally(() => {
//                 hideLoading();  // إخفاء التحميل بعد انتهاء الطلب مهما كانت النتيجة
//                 });
//             } else {
//                 document.getElementById('userIdDisplay').innerText = 'بيانات تيليجرام غير متاحة!';
//             }
//         } else {
//           messageh.innerText = 'تعذر الحصول على بيانات المستخدم.';
//         }
//       } else {
//         messageh.innerText = 'يرجى فتح التطبيق من داخل تيليجرام.';
//       }
    };



function showLoading() {
  document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
  document.getElementById('loadingOverlay').style.display = 'none';
}





const segments = [
  '1,000', 'Hard luck', '5,000','Hard luck','10,000',
  '2,000', '50,000', '100,000','500,000','Hard luck'
];
const segmentCount = segments.length;
const segmentAngle = 2 * Math.PI / segmentCount;

// زاوية المؤشر (إلى الأعلى) بالرصاديان
const indicatorAngle = 3 * Math.PI / 2;

const wheelRadius = canvas.width / 2;
const centerX = canvas.width / 2;
const centerY = canvas.height / 2;

let rotation = 0;
let isSpinning = false;

function drawWheel() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  for (let i = 0; i < segmentCount; i++) {
    let startAngle = i * segmentAngle + rotation;

    // ألوان متناوبة (تُعدل حسب التجميل)
    ctx.fillStyle = i % 2 === 0 ? '#172437' : '#9aa5ab';

    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.arc(centerX, centerY, wheelRadius, startAngle, startAngle + segmentAngle);
    ctx.closePath();
    ctx.fill();

    // اسم القسم
    ctx.save();
    // ctx.fillStyle = '#fff';
    ctx.translate(centerX, centerY);
    ctx.rotate(startAngle + segmentAngle / 2);
    ctx.textAlign = 'right';
    // ctx.font = 'bolder 12px Arial';

if (i === 8) {
      ctx.fillStyle = '#FFD700'; // لون مميز مثلا أحمر
      ctx.font = 'bolder 14px Cairo'; // ممكن تزيد حجم الخط للعنصر المحدد
    } else {
      ctx.fillStyle = '#fff'; // اللون الافتراضي
      ctx.font = 'normal 14px Cairo';
    }



    ctx.fillText(segments[i], wheelRadius - 33, 5);


    if (segments[i]!== 'Hard luck') {
      ctx.save();

      // نقل نقطة الأصل لموضع مناسب لرسم "NSP"
      // هنا نضعها قريبة من مركز القسم نفسه ولكن قليلاً للداخل مثلاً
      ctx.translate(wheelRadius-20, 0);

      // تدوير 90 درجة بالاتجاه الساعاتي
      ctx.rotate(90 * Math.PI / 180);

      if (i === 8) {
      ctx.fillStyle = '#FFD700'; // لون مميز مثلا أحمر
      ctx.font = 'bolder 14px Cairo'; // ممكن تزيد حجم الخط للعنصر المحدد
    } else {
       ctx.fillStyle = '#fff'; // لون كلمة NSP
      ctx.font = 'bold 14px Cairo';
    }


      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';

      ctx.fillText('NSP', 0, 0);

      ctx.restore();
    }

    ctx.restore();
  }
  drawIndicator();
}

function getWinningSegment(rotation) {
  const normalizedRotation = rotation % (2 * Math.PI);

  let adjustedAngle = (indicatorAngle - normalizedRotation + 2 * Math.PI) % (2 * Math.PI);

  let index = Math.floor(adjustedAngle / segmentAngle);

  return segments[index];
}

function drawIndicator() {
  const baseY = centerY - wheelRadius -12;
  const pointerWidth = 24;
  const pointerHeight = 30;

  ctx.save();

  ctx.shadowColor = 'rgba(0,0,0,0.3)';
  ctx.shadowBlur = 6;
  ctx.shadowOffsetX = 0;
  ctx.shadowOffsetY = 3;

  const gradient = ctx.createLinearGradient(centerX, baseY, centerX, baseY + pointerHeight);
  gradient.addColorStop(0, '#f48fb1');
  gradient.addColorStop(1, '#e91e63');
  ctx.fillStyle = gradient;

  ctx.beginPath();
  ctx.moveTo(centerX, baseY + pointerHeight);
  ctx.lineTo(centerX - pointerWidth / 2, baseY);
  ctx.lineTo(centerX + pointerWidth / 2, baseY);
  ctx.closePath();
  ctx.fill();

  ctx.lineWidth = 2;
  ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
  ctx.stroke();

  ctx.restore();
}


function getRandomRotation() {
  const minRotations = 2;  // أقل عدد جولات
  const maxRotations = 6;  // أقصى عدد جولات
  const rotations = Math.random() * (maxRotations - minRotations) + minRotations;
  return rotations * 2 * Math.PI;
}


function spin() {
  if (isSpinning) return;
  isSpinning = true;

   const spinSound = document.getElementById('spinSound');
   const result = document.getElementById('result');
  spinSound.currentTime = 0;  // إعادة الصوت من البداية
  spinSound.play();

  const spins = Math.floor(Math.random() * 5) + 5;
  // const extraRotation = getRandomRotation();
  // const extraRotation = Math.random() * 2 * Math.PI;
  const extraRotation =   userRotate * segmentAngle;
  const targetRotation = spins * 2 * Math.PI + extraRotation;

  const duration = 4000;
  const startTime = performance.now();

  function animate(time) {
    const elapsed = time - startTime;
    const progress = Math.min(elapsed / duration, 1);

    const easeOutQuad = 1 - (1 - progress) * (1 - progress);

    rotation = easeOutQuad * targetRotation;

    drawWheel();

    if (progress < 1) {
      requestAnimationFrame(animate);
    } else {

      const winner = getWinningSegment(rotation);
      if(winner==="Hard luck"){
        result.innerHTML="<h3 class='text-warning'>حظاً موفقاً في المرة القادمة</h3>";
      }else{
        result.innerHTML="<h3>تهانينا, لقد ربحت: <span class='text-warning fw-bold'>"+winner+"NSP</span></h3>";
      }

      isSpinning = true;
      setTimeout(() => {
        spinSound.pause();          // إيقاف الصوت عند نهاية التدوير
      spinSound.currentTime = 0;
      }, 1000);


      fetch('http://localhost/api/ex_rotate', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    // 'X-Requested-With': 'XMLHttpRequest'
                  },
                  body: JSON.stringify({ rotId: rotId }),
                })
                .then(response => {
                  if (!response.ok) throw new Error('خطأ في الشبكة');
                  return response.json();
                })
                .then(data => {
                  console.log('استجابة API:', data);
                  // يمكنك تحديث واجهة المستخدم هنا بناءً على استجابة الخادم
                  messageh.innerHTML = data.message;
                  console.log('قيمة result:', data.message);
                })
                .catch(error => {
                  console.error('خطأ أثناء إرسال الطلب:', error);
                })

    }
  }

  requestAnimationFrame(animate);
}

drawWheel();

spinButton.addEventListener('click', spin);


</script>

  <!-- <script src="script.js"></script> -->
</body>
</html>
