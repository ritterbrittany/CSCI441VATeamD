# !!! DEPRECATED !!! - Automatic utilization of a built-in Threefish-similar cryptographic method

from Crypto.Cipher import AES
from Crypto.Hash import SHA3_512
from Crypto.Random import get_random_bytes
import base64

# Derive a 128-byte key from the Kyber shared secret using SHA3-512
def derive_threefish_key(shared_secret_b64: str) -> bytes:
    shared_secret = base64.b64decode(shared_secret_b64)
    h = SHA3_512.new()
    h.update(shared_secret)
    return h.digest()[:128]  # Stub for Threefish-1024 (should be 128 bytes)

# Temporary stub encryption using AES for demonstration purposes
def encrypt_data(key_128: bytes, plaintext: bytes):
    iv = get_random_bytes(16)
    cipher = AES.new(key_128[:32], AES.MODE_CFB, iv=iv)
    ciphertext = cipher.encrypt(plaintext)
    return base64.b64encode(iv + ciphertext).decode()

def decrypt_data(key_128: bytes, ciphertext_b64: str):
    data = base64.b64decode(ciphertext_b64)
    iv, ciphertext = data[:16], data[16:]
    cipher = AES.new(key_128[:32], AES.MODE_CFB, iv=iv)
    plaintext = cipher.decrypt(ciphertext)
    return plaintext.decode()