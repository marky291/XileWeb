import discord
import os
import requests
from datetime import datetime
from discord.ext import tasks
import sys
import pytz
import json

token = sys.argv[1]
url = sys.argv[2]

intents = discord.Intents.default()
intents.presences = True

client = discord.Client(intents=intents)
session = requests.Session()

@client.event
async def on_ready():
    print(f'Logged in as {client.user}')
    update_activity.start()

@tasks.loop(minutes=1)
async def update_activity():
    # Set the time zone to Frankfurt, Germany
    frankfurt_timezone = pytz.timezone('Europe/Berlin')

    # Get the current time in the specified time zone
    current_time = datetime.now(frankfurt_timezone)

    # Format the time as a string
    time_str = current_time.strftime('%I:%M %p')

    activity = discord.Activity(name=f' {time_str}', type=discord.ActivityType.watching)

    await client.change_presence(activity=activity)

try:
    client.run(token)
except discord.HTTPException as e:
    if e.status == 429:
        print("Too many requests")
    else:
        raise e
