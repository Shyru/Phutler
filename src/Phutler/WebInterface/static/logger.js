/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

function connect(_port)
{
    var uri="ws://"+document.location.hostname+":"+_port;
    console.log("Connecting to",uri);
    websocket = new WebSocket(uri);

    websocket.onopen = function(evt) { onOpen(evt) };
    websocket.onclose = function(evt) { onClose(evt) };
    websocket.onmessage = function(evt) { onMessage(evt) };
    websocket.onerror = function(evt) { onError(evt) };
}

function onOpen(evt)
{
    console.log("onOpen",arguments);
}

function onClose(evt)
{

}

function onMessage(evt)
{
    document.getElementById('log').innerHTML+=evt.data+"\n";
}

function onError(evt)
{

}