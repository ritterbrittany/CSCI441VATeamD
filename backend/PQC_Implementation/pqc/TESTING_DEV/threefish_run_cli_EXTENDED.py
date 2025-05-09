# TO ONLY BE USED FOR DEMO / TESTING PURPOSES

# Gather imports
import argparse
import os
import struct
#from threefish_manual_TESTING import encrypt_block, decrypt_block, expand_key # !!! Grabs 
from threefish_manual_TESTING import generate_key_and_tweak, threefish_encrypt_bytes, threefish_decrypt_bytes # !!! Grabs new additions for large files

def save_bytes(filename, data):
    with open(filename, 'wb') as f:
        f.write(data)

def load_bytes(filename):
    with open(filename, 'rb') as f:
        return f.read()

def save_key(filename, key_words, tweak_words):
    with open(filename, 'w') as f:
        for k in key_words:
            f.write(f"{k}\n")
        for t in tweak_words:
            f.write(f"{t}\n")

def load_key(filename):
    with open(filename, 'r') as f:
        lines = f.read().splitlines()
    key = [int(x) for x in lines[:16]]
    tweak = [int(x) for x in lines[16:18]]
    return key, tweak

def main():
    parser = argparse.ArgumentParser(description="Encrypt or decrypt files or text using Threefish-1024.")
    parser.add_argument('mode', choices=['encrypt', 'decrypt'], help="Mode: encrypt or decrypt")
    parser.add_argument('--infile', help="Input file path")
    parser.add_argument('--outfile', required=True, help="Output file path")
    parser.add_argument('--keyfile', required=True, help="Key file path")
    parser.add_argument('--text', help="Input text to encrypt (instead of a file)")
    parser.add_argument('--print', action='store_true', help="Print decrypted text result")
    args = parser.parse_args()

    if args.mode == 'encrypt':
        key, tweak = generate_key_and_tweak()

        if args.text:
            data = args.text.encode('utf-8')
        elif args.infile:
            data = load_bytes(args.infile)
        else:
            print("Error: Provide either --text or --infile for encryption.")
            return

        encrypted = threefish_encrypt_bytes(data, key, tweak)
        save_bytes(args.outfile, encrypted)
        save_key(args.keyfile, key, tweak)
        print("Encryption complete. Key saved.")

    elif args.mode == 'decrypt':
        key, tweak = load_key(args.keyfile)
        data = load_bytes(args.infile)
        decrypted = threefish_decrypt_bytes(data, key, tweak)
        save_bytes(args.outfile, decrypted)

        if args.print:
            try:
                print("Decrypted text:")
                print(decrypted.rstrip(b'\x00').decode('utf-8'))
            except:
                print("Decrypted data is not valid UTF-8 text.")

        else:
            print("Decryption complete.")

if __name__ == '__main__':
    main()
