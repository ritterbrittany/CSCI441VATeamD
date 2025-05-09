# Manual implementation of Threefish-1024 pulled from "The Skein Hash Family" Version 1.3 documentation
# Official source of documentation can be found at: https://www.schneier.com/wp-content/uploads/2008/10/skein.pdf

# Instantiate vars / constants defined from docs
C240 = 0x1BD11BDAA9FC1A22
NUM_ROUNDS = 80
WORD_BITS = 64
WORD_MASK = 0xFFFFFFFFFFFFFFFF #-- Useful reference for truncating items to 64-bits

ROTATION_CONSTANTS = [
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
# Rotate-left operator - a 64-bit left rotation and a vital part of the PQC algorithm
def rotl64(x, n):
    return ((x<<n) | (x>>(WORD_BITS - n))) & WORD_MASK

# MIX function as illustrated from the docs
#-- This MIX function is utlized as critical feature of "mixing" up the bits throughout the various rounds and permutations
def mix(x0, x1, rdj):
    y0 = (x0+x1) & WORD_MASK #-- Simplified way of performing mod 2^64
    y1 = rotl64(x1, rdj) ^ y0
    return y0, y1

# Helper function to provide the required extra key word and tweak value
def extra_key_values(K_key, T_tweak):
    #-- It is implied that there must be
    assert len(K_key) == 16
    assert len(T_tweak) == 2

    #-- Create 17th key
    key_schedule = list(K_key)