# Automatic utilization of a built-in Kyber key encapsulation method
from pqcrypto.kem.kyber1024 import generate_keypair, encrypt, decrypt
import base64

def generate_kyber_keypair():
    public_key, private_key = generate_keypair()
    return base64.b64encode(public_key).decode(), base64.b64encode(private_key).decode()

def encrypt_with_kyber(public_key_b64, message: bytes):
    public_key = base64.b64decode(public_key_b64)
    ciphertext, shared_secret = encrypt(public_key)
    return {
        "ciphertext": base64.b64encode(ciphertext).decode(),
        "shared_secret": base64.b64encode(shared_secret).decode()
    }

def decrypt_with_kyber(private_key_b64, ciphertext_b64):
    private_key = base64.b64decode(private_key_b64)
    ciphertext = base64.b64decode(ciphertext_b64)
    shared_secret = decrypt(ciphertext, private_key)
    return base64.b64encode(shared_secret).decode()
