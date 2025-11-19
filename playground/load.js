const WebSocket = require("ws");

const URL = "ws://localhost:8080/centrifugo/connection/websocket?cf_ws_frame_ping_pong=true";
// const URL = "ws://localhost:8000/connection/websocket?cf_ws_frame_ping_pong=true";

const CONNECTIONS = 4096;

let connected = 0;
let messages = 0;
let errors = 0;

function createClient(i) {
    const ws = new WebSocket(URL);

    ws.on("open", () => {
        connected++;
        if (connected % 50 === 0) {
            console.log(`Connected: ${connected}`);
        }

        ws.send(JSON.stringify({
            id: i,
            connect: {}
        }));

        // ws.send(JSON.stringify({
        //     id: 10000 + i,
        //     subscribe: {
        //         channel: "otus"
        //     }
        // }));
    });

    ws.on("message", () => {
        messages++;
    });

    ws.on("error", (e) => {
        errors++;
        console.log("error:", e.message);
    });

    ws.on("close", () => {
    });
}

console.log("Starting clients...");

for (let i = 1; i <= CONNECTIONS; i++) {
    createClient(i);
}

setInterval(() => {
    console.log(`STATUS │ connected=${connected} │ errors=${errors} │ messages=${messages}`);
}, 1000);
