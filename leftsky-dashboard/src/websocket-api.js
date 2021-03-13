var socket;
let status = 0;
function startWebsocket() {
  socket = new WebSocket(`ws://${document.domain}:1300`);
  // socket.send('1');
  socket.onopen = () => {
    // 连接成功
    status = 1;
  }
  socket.onclose = () => {
    // 断开连接
    status = 0;
  }

  // socket.onmessage = res => {
  //   if (typeof cb === "function") {
  //     cb(res);
  //   }
  // }
}

function sendCmd(cmd, data) {
  return new Promise((resolve, reject) => {
    // 如果状态不是连接成功，则返回失败
    if (status !== 1) {
      reject(-1);
    }
    // 如果拉取到信息，则返回给promise
    socket.onmessage = res => {
      resolve(JSON.parse(res.data));
    }
    // 超时三秒算作失败
    setTimeout(() => {
      reject(-1);
    }, 3000);
    // 发送命令
    socket.send(JSON.stringify({
      cmd,
      data
    }))
  })
}

export default {
  startWebsocket,
  sendCmd
}
