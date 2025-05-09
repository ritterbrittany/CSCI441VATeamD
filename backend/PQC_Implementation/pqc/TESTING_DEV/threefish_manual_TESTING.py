# Manual implementation of Threefish-1024 pulled from "The Skein Hash Family" Version 1.3 documentation
# Official source of documentation can be found at: https://www.schneier.com/wp-content/uploads/2008/10/skein.pdf

import os
import struct

# Instantiate vars / constants defined from docs
C240 = 0x1BD11BDAA9FC1A22
NUM_ROUNDS = 80
WORD_BITS = 64
WORD_MASK = 0xFFFFFFFFFFFFFFFF
BLOCK_SIZE = 128  # Given the used 16*8 bytes...

ROTATION_SCHEDULE = [
    [24, 13, 8, 47, 8, 17, 22, 37],
    [38, 19, 10, 55, 49, 18, 23, 52],
    [33, 4, 51, 13, 34, 41, 59, 17],
    [5, 20, 48, 41, 47, 28, 16, 25],
    [41, 9, 37, 31, 12, 47, 44, 30],
    [16, 34, 56, 51, 4, 53, 42, 41],
    [31, 44, 47, 46, 19, 42, 44, 25],
    [9, 48, 35, 52, 23, 31, 37, 20],
]

# --- CORE OPERATIONS ---
# Rotate-left and right operators - a 64-bit left/right rotation and a vital part of the PQC algorithm
def rotl64(x, n):
    return ((x << n) | (x >> (WORD_BITS - n))) & WORD_MASK

def rotr64(x, n):
    return ((x >> n) | (x << (WORD_BITS - n))) & WORD_MASK

# MIX function as illustrated from the docs
#-- This MIX function is utlized as critical feature of "mixing" up the bits throughout the various rounds and permutations
def mix(x0, x1, r):
    y0 = (x0 + x1) & WORD_MASK
    y1 = rotl64(x1, r) ^ y0
    return y0, y1

# UNMIX for decryption / reverse operations
def unmix(y0, y1, r):
    x1 = rotr64(y1 ^ y0, r)
    x0 = (y0 - x1) & WORD_MASK
    return x0, x1

# Helper function to provide the required extra key word and tweak value
def expand_key(key_words, tweak_words):
    assert len(key_words) == 16
    assert len(tweak_words) == 2

    # Final key word is parity of first 16 XOR C240
    key_schedule = list(key_words)
    k17 = C240
    for k in key_words:
        k17 ^= k
    key_schedule.append(k17)

    # Third tweak word is XOR of first two
    tweak_schedule = list(tweak_words)
    tweak_schedule.append(tweak_words[0] ^ tweak_words[1])

    return key_schedule, tweak_schedule

# BASE ENCRYPTION ALGORITHM LOGIC
def encrypt_block(P_plaintext, K_key, T_tweak):
    assert len(P_plaintext) == 16
    k, t = expand_key(K_key, T_tweak)
    v = list(P_plaintext)

    for round_num in range(NUM_ROUNDS):
        r_sched = ROTATION_SCHEDULE[round_num % 8]

        for i in range(8):
            v[2*i], v[2*i+1] = mix(v[2*i], v[2*i+1], r_sched[i])

        if (round_num + 1) % 4 == 0:
            s = (round_num + 1) // 4
            for i in range(16):
                v[i] = (v[i] + k[(s + i) % 17]) & WORD_MASK
            v[12] = (v[12] + t[s % 3]) & WORD_MASK
            v[13] = (v[13] + t[(s+1) % 3]) & WORD_MASK
            v[14] = (v[14] + s) & WORD_MASK

    return v

# BASE DECRYPTION / REVERSE ENCRYPTION LOGIC
def decrypt_block(C_ciphertext, K_key, T_tweak):
    assert len(C_ciphertext) == 16
    k, t = expand_key(K_key, T_tweak)
    v = list(C_ciphertext)

    for round_num in reversed(range(NUM_ROUNDS)):
        if (round_num + 1) % 4 == 0:
            s = (round_num + 1) // 4
            for i in range(16):
                v[i] = (v[i] - k[(s + i) % 17]) & WORD_MASK
            v[12] = (v[12] - t[s % 3]) & WORD_MASK
            v[13] = (v[13] - t[(s+1) % 3]) & WORD_MASK
            v[14] = (v[14] - s) & WORD_MASK

        r_sched = ROTATION_SCHEDULE[round_num % 8]

        for i in reversed(range(8)):
            v[2*i], v[2*i+1] = unmix(v[2*i], v[2*i+1], r_sched[i])

    return v

# Multi-block ECB (efficient block cipher method) for large sizes - encryption and respectively reversed logic
def threefish_encrypt_bytes(data_bytes, key_words, tweak_words):
    from struct import pack, unpack
    result = bytearray()
    for i in range(0, len(data_bytes), BLOCK_SIZE):
        block = data_bytes[i:i+BLOCK_SIZE].ljust(BLOCK_SIZE, b'\x00')
        words = list(unpack('<16Q', block))
        encrypted = encrypt_block(words, key_words, tweak_words)
        result.extend(pack('<16Q', *encrypted))
    return bytes(result)

def threefish_decrypt_bytes(data_bytes, key_words, tweak_words):
    from struct import pack, unpack
    result = bytearray()
    for i in range(0, len(data_bytes), BLOCK_SIZE):
        block = data_bytes[i:i+BLOCK_SIZE]
        words = list(unpack('<16Q', block))
        decrypted = decrypt_block(words, key_words, tweak_words)
        result.extend(pack('<16Q', *decrypted))
    return bytes(result)

# Generate keys/tweak as required
def generate_key_and_tweak():
    key_bytes = os.urandom(128)
    tweak_bytes = os.urandom(16)
    key_words = list(struct.unpack('<16Q', key_bytes))
    tweak_words = list(struct.unpack('<2Q', tweak_bytes))
    return key_words, tweak_words