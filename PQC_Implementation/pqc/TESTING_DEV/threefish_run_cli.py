# TO ONLY BE USED FOR DEMO / TESTING PURPOSES

# Gather imports
import argparse
import os
import struct
from threefish_manual_TESTING import encrypt_block, decrypt_block, expand_key # !!! Grabs 

# Helpers to convert between bytes and 64-bit word lists during encryption/decryption operations
def bytes_to_words(b):
    return list(struct.unpack('<16Q', b))

def words_to_bytes(words):
    return struct.pack('<16Q', *words)

# KEY GENERATION
def generate_key_and_tweak():
    key_bytes = os.urandom(128)
    tweak_bytes = os.urandom(16)
    key_words = bytes_to_words(key_bytes)
    tweak_words = list(struct.unpack('<2Q', tweak_bytes))
    return key_words, tweak_words

def main():
    parser = argparse.ArgumentParser(description="Encrypt and decrypt text using Threefish-1024.")
    parser.add_argument("--text", required=True, help="The text to encrypt and decrypt.")
    args = parser.parse_args()

    text = args.text.encode("utf-8")
    if len(text) < 128:
        text += b'\x00' * (128 - len(text))
    elif len(text) > 128:
        print("Input longer than 128 bytes, truncating to first 128 bytes.")
        text = text[:128]

    block = bytes_to_words(text)
    key, tweak = generate_key_and_tweak()

    encrypted = encrypt_block(block, key, tweak)
    decrypted = decrypt_block(encrypted, key, tweak)

    encrypted_bytes = words_to_bytes(encrypted)
    decrypted_bytes = words_to_bytes(decrypted)

    print("\nEncrypted (hex):", encrypted_bytes.hex())
    print("Decrypted (raw):", decrypted_bytes.rstrip(b'\x00').decode("utf-8", errors="ignore"))

if __name__ == "__main__":
    main()
