from flask import Flask, redirect, Request
import urllib.request
import urllib.parse

app = Flask(__name__)

@app.route('/')
@app.route('/<path:path>', methods=['GET', 'POST', 'PUT', 'DELETE'])
def proxy(path=''):
    import requests

    # Forward the request to Laravel
    url = f'http://192.168.1.16:8000/{path}'

    # Forward headers and data
    headers = {key: value for key, value in request.headers if key != 'Host'}

    try:
        if request.method == 'GET':
            resp = requests.get(url, headers=headers, params=request.args)
        elif request.method == 'POST':
            resp = requests.post(url, headers=headers, data=request.get_data(), params=request.args)
        else:
            resp = requests.request(request.method, url, headers=headers, data=request.get_data())

        # Return the response
        return resp.content, resp.status_code, resp.headers.items()
    except Exception as e:
        return f'Proxy error: {str(e)}', 500

if __name__ == '__main__':
    from flask import request
    app.run(ssl_context=('cert.pem', 'key.pem'), host='0.0.0.0', port=8443)
